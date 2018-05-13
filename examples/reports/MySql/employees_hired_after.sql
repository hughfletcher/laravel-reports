-- {
--     "name": "Employee Hired After",
--     "description": "Lists all employees hired after a specified date.",
--     "connection": "mysql",
--     "variables" : [
--         {
--             "name": "date",
--             "description": "Pick a date.",
--             "display": "Date",
--             "type": "date",
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
where e.hired_at >= "{{ $date }}"
