-- {
--     "name": "Employee Detail",
--     "connection": "mysql",
--     "vertical": true,
--     "variables" : [
--         {
--             "name": "id",
--             "display": "Id",
--             "type": "text",
--             "rules": "required|numeric"
--         }
--     ]
-- }

select * from employee where id = {{ $id }}
