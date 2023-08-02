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
print("Extracting branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[code]
      ,[name]
      ,[company_name]
      ,[operated_by]
      ,[address]
      ,[tinno]
      ,iif([default] = 1,1,0) as [default]
      ,iif([main] = 1,1,0) as [main]
      ,[web]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
      ,[concurrency_id]
    FROM [diQTech_db].[dbo].[diQt_Branch]
    WHERE code = '""" + mBCode + """'""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath + 'mdatapos.Branch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting branch table done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting terminal table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[branch_id]
      ,[name]
      ,[link_server]
      ,[serial_no]
      ,[permit_no]
      ,[machine_no]
      ,[pos_id]
      ,iif([pole_display] = 1,1,0) as [pole_display]
      ,[pole_display_port]
      ,[orno]
      ,[prefix]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
      ,[concurrency_id]
      ,[zcounter]
      ,[transaction_no]
      ,[voucher_no]
      ,iif([default] = 1,1,0) as [default]
      ,[server_instance]
      ,[server_password]
      ,[server_user]
    FROM [diQTech_db].[dbo].[diQt_Terminal]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath + 'mdatapos.Terminal.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting terminal table done... " + dt_string + " " + mBCode) 
print("===================================")
conn.close()  # <--- Close the connection
