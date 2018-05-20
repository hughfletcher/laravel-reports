<?php namespace Reports;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Application;
use Reports\Contracts\Headers\{Header, AltersQuery};
use Carbon\Carbon;

class Report implements Arrayable
{
    private $app;
    protected $raw_query;
    protected $raw_headers = [];
    private $full_path;
    private $data;
    private $headers = [];

    public function __construct(Application $app, $path)
    {
        $this->fs = $app['filesystem']->disk(config('reports.reports.disk'));
        $this->db = $app['db'];
        $this->validator = $app['validator'];
        $this->app = $app;
        $this->full_path = $path;
        $this->initData();
        $this->parseFile($path);
        if (empty($this->data['header_errors'])) {
            $this->resolveHeaders();
        }
    }

    public function initData()
    {
        $this->data = [
            'name' => null,
            'description' => null,
            'connection' =>null,
            'lastModified' =>null,
            'headers' => [],
            'header_errors' => [],
            'macro_errors' => null,
            'ready' => false,
            'report_url' => route('reports.report'),
            'report_path' => str_replace(config('reports.reports.path') . '/', '', $this->full_path),
            'macros' => [],
            'vertical' => false,
            'query' => null,
            'auth' => null
        ];
    }

    public function macros($macros = [])
    {
        $rules = [];
        $this->data['macros'] = $macros;

        if (!isset($this->data['headers']['variables']) && empty($this->data['header_errors'])) {
            $this->data['ready'] = true;
            return $this;
        }


        if (!empty($this->data['header_errors']) || empty($macros)) {

            return $this;
        }


        foreach ($this->data['headers']['variables']->toArray() as $key => $value) {
            $rules[$value['name']] = isset($value['rules']) ? $value['rules'] : '';
        }

        $validator = $this->validator->make($macros, $rules);

        if ($validator->fails()) {

            $this->data['macro_errors'] = $validator->errors();
            return $this;
        }

        if (empty($this->data['header_errors'])) {
            $this->data['ready'] = true;
        }
        return $this;
    }

    public function run()
    {
        if ($this->data['ready']) {

            $vars = [];
            foreach ($this->data['headers'] as $header) {

                if ($header instanceof AltersQuery) {
                    $vars = array_merge($vars, $header->process($this->macros));
                }

            }

            if ($this->data['connection'] == 'php') {
                $result = collect($this->eval($this->raw_query, $vars));
            } else {
                $php = $this->app['blade.compiler']->compileString($this->raw_query);

                $this->data['query'] = $this->render($php, $vars);

                $final = $original = collect($this->db->connection($this->data['connection'])->select($this->data['query']))->map(function($x){ return (array) $x; });
            }

            if ($original->isEmpty()) {
                return ['message' => 'No data available.', 'query' => $this->data['query']];
            }

            if ($this->data['vertical']) {
                $vertical = [];
                foreach($final->first() as $key => $value) {
                    $vertical[] = ['Key' => $key, 'Value' => $value];
                }
                $final = collect($vertical);
            }

            return $final;
        }

    }

    private function eval($code, $vars)
    {
        extract($vars);
        return eval($code);
    }

    private function render($__php, $__data)
        {
            $obLevel = ob_get_level();
            ob_start();
            extract($__data, EXTR_SKIP);
            try {
                eval('?' . '>' . $__php);
            } catch (Exception $e) {
                while (ob_get_level() > $obLevel) ob_end_clean();
                throw $e;
            } catch (Throwable $e) {
                while (ob_get_level() > $obLevel) ob_end_clean();
                throw new FatalThrowableError($e);
            }
            return ob_get_clean();
        }

    protected function resolveHeaders()
    {
        $root = [];
        foreach ($this->raw_headers as $key => $value) {
            if (!is_array($value)) {
                $root[$key] = $value;
            } else {
                $class = 'Reports\Headers\\' . ucfirst(strtolower((is_array($value) ? str_singular($key) : $key))) . 'Header';


                $header = resolve($class);
                $header->create($value);


                if (!$this->validateHeader($header, $value)) {

                    continue;
                }

                 $this->data['headers'][strtolower($key)] = $header;

            }
        }

        $header = resolve('Reports\Headers\MetaHeader');
        if ($this->validateHeader($header, $root)) {
            $this->data = array_merge($this->data, $root);
        }
    }

    protected function validateHeader(Header $header, array $array)
    {
        if ($header instanceof Arrayable) {
            $array = ['array' => $array];
        }

        $validator = $this->validator->make($array, $header->rules($array));

        if ($validator->fails()) {
            $this->data['header_errors'][get_class($header)] = $validator->errors();
            return false;
        }
        return true;
    }


    protected function parseFile($path)
    {
        // $data['ext'] = pathinfo($path, PATHINFO_EXTENSION);
        // $data['filename'] = pathinfo($path, PATHINFO_BASENAME);

        $raw = $this->fs->get($path);
        $this->data['lastModified'] = Carbon::createFromTimestamp($this->fs->lastModified($path));
        //convert EOL to unix format
		$raw = str_replace(array("\r\n","\r"),"\n", $raw);

        if (strpos($raw, "\n\n") === false) {
            $this->errors[] = 'Report missing headers - ' . $path;
		}

        list($header, $this->raw_query) = explode("\n\n", $raw, 2);

        $lines = explode("\n", $header);

        $fixed_lines = [];
		foreach($lines as $line) {
            //if empty or the line doesn't start with a comment character, skip
			if (empty($line) || (!in_array(substr($line, 0, 2), ['--','/*','//',' *', '<?']) && $line[0] !== '#')) {
                // $this->data['config_errors'] = 'Incorrect syntax in header of file. - ' . $path;
                break;
            }

            if (substr($line, 0, 5) == '<?php') {
                continue;
            }
			///remove comment from start of line and skip if empty
			$line = trim(ltrim($line, "-*/# \t"));
			if(!$line) {
                // $this->data['config_errors']= 'Incorrect syntax in header of file. - ' . $path;
                break;
            }

			$fixed_lines[] = $line;
		}
        // dd($fixed_lines);
		$lines = $fixed_lines;
        $json = implode('', $lines);

        $validator = $this->validator->make(['header' => $json], ['header' => 'required|json']);

        if ($validator->fails()) {
            $this->data['header_errors']['Syntax'] = $validator->errors();
        }

        $this->raw_headers = json_decode($json, true);
    }

    public function toArray()
    {
        if (!$this->data['ready']) {
            $array = ['success' => false];
            if ($this->data['header_errors'] || $this->data['macro_errors']) {
                return ['success' => false, 'message' => 'There are issues with your report syntax configuration.'];
            }
            return ['success' => false, 'message' => 'This report needs more information before running.'];
        }
        return $this->run()->toArray();
    }

    public function config()
    {
        return $this->data;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

}
