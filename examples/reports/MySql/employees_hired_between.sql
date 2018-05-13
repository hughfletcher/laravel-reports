-- {
--     "name": "Employee Hired Between",
--     "description": "Lists all employees hired after a specified date.",
--     "connection": "mysql",
--     "variables" : [
--         {
--             "name": "daterange",
--             "description": "Pick a date range.",
--             "display": "Date",
--             "type": "daterange",
--             "rules": "",
--             "format": "Y-m-d H:i:s",
--             "default": "2017-05-28"
--         }
--     ]
-- }

select
    e.name as Name,
    e.salary as Salary,
    ep.title as Title,
    e.hired_at as "Hire Date"
from employee as e
join employee_position as ep on e.position_id = ep.id
where e.hired_at >= "{{ $start_daterange }}" and e.hired_at <= "{{ $end_daterange }}"
