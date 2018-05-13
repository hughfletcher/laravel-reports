-- {
--     "name": "Positions",
--     "connection": "mysql",
--     "ignore": true,
--     "variables": [
--         {
--             "name": "search",
--             "type": "select",
--             "options": [
--                 {"value": "Developer", "display": "Developer"},
--                 {"value": "Manager", "display": "Manager"},
--                 {"value": "Designer", "display": "Designer"}
--             ],
--             "rules": "in:Developer,Manager,Designer",
--             "default": "Developer"
--         }
--     ]
-- }

select
    id,
    title
from employee_position
where title like '%{{ $search }}%'
