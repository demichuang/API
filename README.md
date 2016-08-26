# API
url - https://challenge-demichuang.c9users.io/API/Api.php?參數=值

ex: https://challenge-demichuang.c9users.io/API/Api.php?action=addUser&username=jj


1.新增帳號

api名稱 - addUser

參數1 - username(帳號)


2.取得餘額

api名稱 - getBalance

參數1 - username(帳號)


3.轉帳(轉進)

api名稱 - in

參數1 - username(帳號)

參數2 - money(轉帳金額)

參數3 - transid(轉帳序號)


4.轉帳(轉出)

api名稱 - out

參數1 - username(帳號)

參數2 - money(轉帳金額)

參數3 - transid(轉帳序號)


5.檢查轉帳狀態

api名稱 - getStatus

參數1 - transid(轉帳序號)