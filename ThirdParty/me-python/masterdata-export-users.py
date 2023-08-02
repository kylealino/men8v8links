import pyodbc
import sys
import os
import pandas as pd
import warnings
from zipfile import ZipFile
from datetime import datetime

if len(sys.argv) == 1:
    print("Parameter's required: Branch_Code")
    sys.exit(0)
mBCode = sys.argv[1]
mPath = sys.argv[2]
warnings.filterwarnings('ignore')
print(mBCode)
server = '192.168.8.41'
database = 'diQtech_db'
username = 'mesa'
password = 'ITzTheT3am'
conn = pyodbc.connect(
    'DRIVER={ODBC Driver 17 for SQL Server};SERVER=' + server + ';DATABASE=' + database + ';UID=' + username + ';PWD=' + password)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting user table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT u.[id]
      ,u.[id_no]
      ,u.[first_name]
      ,u.[middle_name]
      ,u.[last_name]
      ,u.[contact_num]
      ,u.[user_name]
      ,u.[password]
      ,u.[remarks]
      ,CONVERT(VARCHAR(23),u.[date_changed],121) as date_changed
      ,iif(u.[disable] = 1,1,0) as [disable]
      ,iif(u.[logged] = 1,1,0) as [logged]
      ,u.[computer]
      ,u.[role_id]
      ,iif(u.[allow_reset] = 1,1,0) as [allow_reset]
      ,[concurrency_id]
      ,iif(u.[allow_void_sales] = 1,1,0) as [allow_void_sales]
      ,iif(u.[allow_override_discount] = 1,1,0) as [allow_override_discount]
      ,iif(u.[allow_return] = 1,1,0) as [allow_return]
      ,iif(u.[allow_print_xyz] = 1,1,0) as [allow_print_xyz]
      ,iif(u.[allow_reprint] = 1,1,0) as [allow_reprint]
      ,iif(u.[allow_open_close_shift] = 1,1,0) as [allow_open_close_shift]
      ,iif(u.[allow_cash_pickup] = 1,1,0) as [allow_cash_pickup]
      ,iif(u.[allow_bargain] = 1,1,0) as [allow_bargain]
      ,u.[report_days]
      ,iif(u.[allow_cancel_transaction] = 1,1,0) as [allow_cancel_transaction]
      ,iif(u.[allow_multiple_payment] = 1,1,0) as [allow_multiple_payment]
      ,iif(u.[allow_refund_transaction] = 1,1,0) as [allow_refund_transaction]
      ,iif(u.[allow_remove_onhold_transaction] = 1,1,0) as [allow_remove_onhold_transaction]
      ,iif(u.[allow_restore_data] = 1,1,0) as [allow_restore_data]
      ,iif(u.[allow_reward_card] = 1,1,0) as [allow_reward_card]
      ,iif(u.[allow_sales_by_item] = 1,1,0) as [allow_sales_by_item]
      ,iif(u.[allow_sales_journal] = 1,1,0) as [allow_sales_journal]
      ,iif(u.[allow_view_pos_sales] = 1,1,0) as [allow_view_pos_sales]
      ,iif(u.[allow_void_item] = 1,1,0) as [allow_void_item]
      ,iif(u.[allow_voucher] = 1,1,0) as [allow_voucher]
    FROM [diQTech_db].[dbo].[diQt_User] as u
    JOIN [diQTech_db].[dbo].[diQt_UserBranch] as ub
    ON u.[id] = ub.[user_id]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath + 'mdatapos.User.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting user table done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting user access table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT ua.[id]
      ,ua.[user_id]
      ,ua.[module_id]
      ,iif(ua.[select] = 1,1,0) as [select]
      ,iif(ua.[insert] = 1,1,0) as [insert]
      ,iif(ua.[update] = 1,1,0) as [update]
      ,iif(ua.[delete] = 1,1,0) as [delete]
      ,iif(ua.[print] = 1,1,0) as [print]
      ,CONVERT(VARCHAR(23),ua.[date_changed],121) as date_changed
      ,[concurrency_id]
    FROM [diQTech_db].[dbo].[diQt_UserAccess] as ua
    JOIN [diQTech_db].[dbo].[diQt_UserBranch] as ub
    ON ua.[id] = ub.[user_id]
    WHERE ub.branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.UserAccess.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting user access table done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting user branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[user_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_UserBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.UserBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting user branch table done... " + dt_string + " " + mBCode)

print("===================================")
conn.close()  # <--- Close the connection
