# API
url - https://challenge-demichuang.c9users.io/API/Api.php?參數=值

ex: https://challenge-demichuang.c9users.io/API/Api.php?action=addUser&username=jj


###1.新增帳號

參數:

action = addUser

username = (string)(帳號)

*帳號不能重複

###2.取得餘額

參數:

action = getBalance

username = (string)(帳號)

###3.轉帳(轉進)

參數:

action = in

username = (string)(帳號)

money = (int)(轉帳金額)

transid = (int)(轉帳序號)

*轉帳金額上限：1000000

*轉帳序號不能重複

###4.轉帳(轉出)

參數:

action = out

username = (string)s(帳號)

money = (int)(轉帳金額)

transid = (int)(轉帳序號)

*轉帳金額上限：1000000

*轉帳序號不能重複

###5.檢查轉帳狀態

參數:

action = getStatus

transid = (int)(轉帳序號)