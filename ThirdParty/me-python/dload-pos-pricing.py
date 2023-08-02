import pyodbc
import sys
import random
import string
import pandas as pd
import warnings 
from datetime import datetime
import glob
import os
import mysql.connector
from mysql.connector import Error
warnings.filterwarnings('ignore')

def get_random_string(length):
    letters = string.ascii_lowercase
    result_str = ''.join(random.choice(letters) for i in range(length))
    return result_str
    # print("Random string of length", length, "is:", result_str)

if len(sys.argv) < 4:
    print("Parameter's required: Branch_Code Path temptoken")
    sys.exit(0)
print("total parameters:",len(sys.argv))
mBCode = sys.argv[1]
mPath = sys.argv[2]
metmptkn = sys.argv[3]
warnings.filterwarnings('ignore')
print(mBCode)
server = '192.168.8.41' 
database = 'diQtech_db' 
username = 'meedi' 
password = 'Sal3s-EDI' 
cnxn = pyodbc.connect('DRIVER={ODBC Driver 17 for SQL Server};SERVER='+server+';DATABASE='+database+';UID='+username+';PWD='+ password)
cursor = cnxn.cursor()
strqry = "select '" + mBCode + "' as [Branch_Code],aa.[price],aa.[product_id] from [diQtech_db].[dbo].[diQt_Pricing] aa where aa.[branch_id] = " + """
(select top 1 [id] from [diQtech_db].[dbo].[diQt_Branch] where [code] = '""" + mBCode + "') " 
query = pd.read_sql_query(strqry,cnxn)
df = pd.DataFrame(query)
mfiledata = '/tmp/mdatapos.price.txt' 
if os.path.exists(mfiledata):
	os.remove(mfiledata)
df.to_csv(mfiledata, index=False, sep='\t')
if os.path.exists(mfiledata):
	try:
		mydb1 = 'ap2'
		mydb2 = 'ap2_branch'
		mydbtmp = 'pansamantala'
		connection = mysql.connector.connect(host='192.168.8.38',
											 database='ap2',
											 user='meedi',
											 password='m3EDI_DATA',allow_local_infile=True)
		if connection.is_connected():
			db_Info = connection.get_server_info()
			print("Connected to MySQL Server version ", db_Info)
			cursor = connection.cursor(dictionary=True) 
			print(mfiledata)
			metbltmp = mydbtmp + ".`tmp_data_mposprice_" + metmptkn + "`"
			meqry = "drop table if exists " + metbltmp
			cursor.execute(meqry)
			connection.commit()
			meqry = "create table if not exists " + metbltmp + """(
			`recid` bigint(10) NOT NULL AUTO_INCREMENT,
			`MBRANCH_CODE` varchar(35) DEFAULT '',
			`MPROD_PRICE` decimal(15,2) DEFAULT 0.00,
			`MPROD_ID` int(10) DEFAULT 0,
			KEY `recid` (`recid`),
			KEY `idx02` (`MBRANCH_CODE`),
			KEY `idx03` (`MPROD_ID`) 
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"""
			cursor.execute(meqry)
			connection.commit()
			meqry = "LOAD DATA LOCAL INFILE '" + mfiledata + "' INTO TABLE " + metbltmp + """ 
			FIELDS TERMINATED BY '\t' 
			LINES TERMINATED BY '\n' 
			ignore 1 lines 
			(`MBRANCH_CODE`,`MPROD_PRICE`,`MPROD_ID`)"""
			cursor.execute(meqry)
			connection.commit()
			print("Affected data loaded: ",cursor.rowcount)
			print(metbltmp)
	except Error as e:
		print("Error while connecting to MySQL", e)
	finally:
		if (connection.is_connected()):
			cursor.close()
			connection.close()
			print("MySQL connection is closed")   
		
print("=============================")
cursor.close()
cnxn.close()     #<--- Close the connection
#os.remove(m_fileload)
