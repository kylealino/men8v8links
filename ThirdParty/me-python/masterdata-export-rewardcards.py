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
print("Extracting reward card table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[card_no]
      ,[account_no]
      ,iif([replacement] = 1,1,0) as [replacement]
      ,iif([renewal] = 1,1,0) as [renewal]
      ,[initial_points]
      ,[forwarded_reward_card_id]
      ,[forwarded_points]
      ,[points]
      ,[customer_id]
      ,[sales_id]
      ,[terminal_id]
      ,CONVERT(VARCHAR(23),[date_issued],121) as [date_issued]
      ,CONVERT(VARCHAR(23),[valid_until],121) as [valid_until]
      ,iif([replaced] = 1,1,0) as [replaced]
      ,CONVERT(VARCHAR(23),[date_activated],121) as [date_activated]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_RewardCard]""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath + 'mdatapos.RewardCard.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting reward card table done... " + dt_string + " " + mBCode)

print("===================================")
conn.close()  # <--- Close the connection
