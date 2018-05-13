-- {
--     "name": "Employee By Postion Report",
--     "description": "Lists all employees by position.",
--     "connection": "mysql",
--     "variables" : [
--         {
--             "name": "position",
--             "description": "Select a postion.",
--             "display": "Position",
--             "type": "select",
--             "rules": "required",
--             "report_options": {
--                 "report": "MySql/positions.sql",
--                 "display": "title",
--                 "value": "id",
--                 "macros": {
--                     "search": "Manager"
--                 }
--             }
--         }
--     ]
-- }

select
    e.name as Name,
    e.salary as Salary,
    ep.title as Title
from employee as e
join employee_position as ep on e.position_id = ep.id
where e.position_id = {{ $position }}
