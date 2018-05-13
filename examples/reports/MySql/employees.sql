-- {
--     "name": "Employee Report",
--     "description": "Lists all employees.",
--     "connection": "mysql",
--     "variables" : [
--         {
--             "name": "search",
--             "description": "Search for employee.",
--             "display": "Search",
--             "rules": "",
--             "type": "text"
--         },
--         {
--             "name": "salary",
--             "description": "Specify salary range.",
--             "display": "Salary",
--             "rules": "numeric",
--             "type": "text",
--             "modifier": ["<", ">", "="],
--             "default" : {"modifier": "<", "value": "50000"}
--         }
--     ]
-- }

select
    e.name as Name,
    e.salary as Salary,
    ep.title as Title
from employee as e
join employee_position as ep on e.position_id = ep.id
where e.name like "%{{ $search }}%"
and e.salary {!! $modifier_salary !!} {{ $salary }};
