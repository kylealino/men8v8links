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
print("Extracting Product table... " + dt_string + " " + mBCode)
query = pd.read_sql_query("""
	    SELECT p.[id]
          ,p.[product_id]
          ,p.[category_id]
          ,p.[brand_id]
          ,p.[unit_id]
          ,p.[quantity_per_unit]
          ,p.[part_no]
          ,p.[stock_no]
          ,p.[name]
          ,p.[receipt_name]
          ,p.[barcode]
          ,p.[store_code]
          ,p.[cost]
          ,p.[stock]
          ,p.[stock_in_pieces]
          ,iif(p.[vatable] = 1,1,0) as vatable
          ,p.[model] 
          ,iif(p.[sc_discount] = 1,1,0) as sc_discount
          ,p.[sc_discount_value]
          ,iif(p.[sspt] = 1,1,0) as sspt
          ,p.[srp]
          ,p.[wp]
          ,CONVERT(VARCHAR(23),p.[date_changed],121) as date_changed
          ,iif(p.[disable] = 1,1,0) as disable
      FROM [diQTech_db].[dbo].[diQt_Product] p
      JOIN [diQTech_db].[dbo].[diQt_ProductBranch] pb
      ON pb.product_id = p.id
      WHERE pb.branch_id = (SELECT top(1) [id] FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn)
df = pd.DataFrame(query)
mfiledata = mPath + 'mdatapos.Product.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Product table done... " + dt_string + " "  + mBCode)
now = datetime.now()

dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Pricing table... " + dt_string + " " + mBCode)
query = pd.read_sql_query("""

    SELECT [id]
      ,[price_id]
      ,[product_id]
      ,[branch_id]
      ,[price]
      ,CONVERT(VARCHAR(23),[date_changed],121) as [date_changed]
    FROM [diQTech_db].[dbo].[diQt_Pricing]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn)
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.ProductPricing.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Product Pricing table done... " +  dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Branch table... " + dt_string + " " + mBCode)
query = pd.read_sql_query("""

    SELECT [id]
      ,[product_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
      ,iif([damage] = 1,1,0) as [damage]
    FROM [diQTech_db].[dbo].[diQt_ProductBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn)
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.ProducBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Branch done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Detail table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT pd.[id]
      ,iif(pd.[points_allowed] = 1,1,0) as [points_allowed]
      ,pd.[product_group_id]
      ,pd.[product_sub_group_id]
      ,pd.[product_class_id]
      ,CONVERT(VARCHAR(23),pd.[date_changed],121) as date_changed
      ,iif(pd.[membership]  = 1,1,0) as [membership]
      ,iif(pd.[pwd_allowed] = 1,1,0) as [pwd_allowed]
      ,pd.[pwd_discount]
      ,pd.[product_type_id]
      ,iif(pd.[damage] = 1,1,0) as [damage]
    FROM [diQTech_db].[dbo].[diQt_ProductDetail] pd
    JOIN [diQTech_db].[dbo].[diQt_ProductBranch] pb
    ON pd.id = pb.product_id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.ProductDetail.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Detail done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Type table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[name]
      ,[amount_to_earn_point]
      ,iif([reward_card_tier] = 1,1,0) as [reward_card_tier]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_ProductType]""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.ProductType.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Type done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Class table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[name]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
      ,[concurrency_id]
    FROM [diQTech_db].[dbo].[diQt_ProductClass]""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.ProductClass.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Class done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Group table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[name]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
      ,[concurrency_id]
    FROM [diQTech_db].[dbo].[diQt_ProductGroup]""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.ProductGroup.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Group done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Sub Group table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT [id]
      ,[name]
      ,CONVERT(VARCHAR(23),[date_changed],121) as date_changed
      ,iif([disable] = 1,1,0) as [disable]
      ,[concurrency_id]
  FROM [diQTech_db].[dbo].[diQt_ProductSubGroup]""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.ProductSubGroup.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Product Sub Group done... " + dt_string + " " + mBCode)
print("===================================")
conn.close()  # <--- Close the connection
