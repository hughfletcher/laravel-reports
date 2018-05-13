-- {
--     "name": "Employee Drilldown",
--     "description": "Lists all employees.",
--     "connection": "mysql",
--     "filters": [
--         {
--             "filter": "drilldown",
--             "column": "Name",
--             "params": {
--                 "report": "mysql/employee.sql",
--                 "macros": {
--                     "id": "id"
--                 }
--             }
--         },
--         {
--             "filter": "hide",
--             "column": "id"
--         }
--     ]
-- }

select
    name as Name,
    salary as Salary,
    id
from employee
