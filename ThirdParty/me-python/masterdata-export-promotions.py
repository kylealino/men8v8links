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
print("Extracting Promo Discount table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pd.[id]
      ,pd.[code]
      ,pd.[name]
      ,CONVERT(VARCHAR(23),pd.[start_date],121) as [start_date]
      ,CONVERT(VARCHAR(23),pd.[end_date],121) as [end_date]
      ,iif(pd.[is_discount_percent] = 1,1,0) as [is_discount_percent]
      ,iif(pd.[is_discount_amount] = 1,1,0) as [is_discount_amount]
      ,iif(pd.[is_fixed_price] = 1,1,0) as [is_fixed_price]
      ,pd.[value]
      ,pd.[product_ids]
      ,CONVERT(VARCHAR(23),pd.[date_changed],121) as date_changed
      ,iif(pd.[disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoDiscount] as pd
    JOIN [diQTech_db].[dbo].[diQt_PromoDiscountBranch] pdb
    ON pd.id = pdb.promo_discount_id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath + 'mdatapos.PromoDiscount.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Discount done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Discount Branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT [id]
      ,[promo_discount_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as [date_changed]
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoDiscountBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoDiscountBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Discount Branch done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy X Take Y table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pbxty.[id]
      ,pbxty.[code]
      ,pbxty.[name]
      ,CONVERT(VARCHAR(23),pbxty.[start_date],121) as [start_date]
      ,CONVERT(VARCHAR(23),pbxty.[end_date],121) as [end_date]
      ,pbxty.[quantity]
      ,pbxty.[take]
      ,iif(pbxty.[is_discount_percent] = 1,1,0) as [is_discount_percent]
      ,iif(pbxty.[is_discount_amount] = 1,1,0) as [is_discount_amount]
      ,iif(pbxty.[is_fixed_price] = 1,1,0) as [is_fixed_price]
      ,pbxty.[value]
      ,pbxty.[product_ids_buy]
      ,pbxty.[product_ids_take]
      ,CONVERT(VARCHAR(23),pbxty.[date_changed],121) as [date_changed]
      ,iif(pbxty.[disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoBuyXTakeY] as pbxty
    JOIN [diQTech_db].[dbo].[diQt_PromoBuyXTakeYBranch] as pbxtyb
    ON  pbxtyb.promo_buy_x_take_y_id = pbxty.id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoBuyXTakeY.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy X Take Y done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy X TakeY Branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT [id]
      ,[promo_buy_x_take_y_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as [date_changed]
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoBuyXTakeYBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoBuyXTakeYBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy X Take Y Branch done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Threshold table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pt.[id]
      ,pt.[code]
      ,pt.[name]
      ,CONVERT(VARCHAR(23),pt.[start_date],121) as [start_date]
      ,CONVERT(VARCHAR(23),pt.[end_date],121) as [end_date]
      ,pt.[amount]
      ,pt.[discount]
      ,pt.[product_ids]
      ,CONVERT(VARCHAR(23),pt.[date_changed],121) as [date_changed]
      ,iif(pt.[disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoThreshold] as pt
    JOIN [diQTech_db].[dbo].[diQt_PromoThresholdBranch] as ptb
    ON pt.id = ptb.promo_threshold_id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoThreshold.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Threshold done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Threshold Branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT [id]
      ,[promo_threshold_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as [date_changed]
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoThresholdBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoThresholdBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Threshold Branch done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Voucher table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pv.[id]
      ,pv.[code]
      ,pv.[name]
      ,CONVERT(VARCHAR(23),pv.[start_date],121) as [start_date]
      ,CONVERT(VARCHAR(23),pv.[end_date],121) as [end_date]
      ,pv.[amount]
      ,pv.[discount]
      ,CONVERT(VARCHAR(23),pv.[date_changed],121) as [date_changed]
      ,iif(pv.[disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoVoucher] as pv
    JOIN [diQTech_db].[dbo].[diQt_PromoVoucherBranch] as pvb
    ON pv.id = pvb.promo_voucher_id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoVoucher.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Voucher done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Voucher Branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT [id]
      ,[promo_voucher_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as [date_changed]
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoVoucherBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoVoucherBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Voucher Branch done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Voucher Code table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pvc.[id]
      ,pvc.[promo_voucher_id]
      ,pvc.[code]
      ,iif(pvc.[used] = 1,1,0) as [used]
      ,CONVERT(VARCHAR(23),pvc.[date_changed],121) as [date_changed]
      ,iif(pvc.[disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoVoucherCode] as pvc
    JOIN [dbo].[diQt_PromoVoucherBranch] as pvb
    ON pvc.promo_voucher_id = pvb.id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoVoucherCode.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Voucher Code done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy Any X Price Y table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pbaxpy.[id]
      ,pbaxpy.[code]
      ,pbaxpy.[name]
      ,CONVERT(VARCHAR(23),pbaxpy.[start_date],121) as [start_date]
      ,CONVERT(VARCHAR(23),pbaxpy.[end_date],121) as [end_date]
      ,pbaxpy.[quantity]
      ,pbaxpy.[price]
      ,pbaxpy.[product_ids]
      ,CONVERT(VARCHAR(23),pbaxpy.[date_changed],121) as [date_changed]
      ,iif(pbaxpy.[disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoBuyAnyXPriceY] as pbaxpy
    JOIN [diQTech_db].[dbo].[diQt_PromoBuyAnyXPriceYBranch] as pbaxpyb
    ON pbaxpy.id = pbaxpyb.promo_buy_any_x_price_y_id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoBuyAnyXPriceY.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy Any X Price Y done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy Any X Price Y Branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT [id]
      ,[promo_buy_any_x_price_y_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as [date_changed]
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoBuyAnyXPriceYBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoBuyAnyXPriceYBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Buy Any X Price Y Branch done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Damage table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pd.[id]
      ,pd.[code]
      ,pd.[name]
      ,CONVERT(VARCHAR(23),pd.[start_date],121) as [start_date]
      ,CONVERT(VARCHAR(23),pd.[end_date],121) as [end_date]
      ,[product_id]
      ,iif(pd.[is_peso_discount] = 1,1,0) as [is_peso_discount]
      ,iif(pd.[is_percent_discount] = 1,1,0) as [is_percent_discount]
      ,pd.[discount]
      ,CONVERT(VARCHAR(23),pd.[date_changed],121) as [date_changed]
      ,iif(pd.[disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoDamage] as pd
    JOIN [diQTech_db].[dbo].[diQt_PromoDamageBranch] as pdb
    ON pd.id = pdb.promo_damage_id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoDamage.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Damage done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Damage Branch table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT [id]
      ,[promo_damage_id]
      ,[branch_id]
      ,CONVERT(VARCHAR(23),[date_changed],121) as [date_changed]
      ,iif([disable] = 1,1,0) as [disable]
    FROM [diQTech_db].[dbo].[diQt_PromoDamageBranch]
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoDamageBranch.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Damage Branch done... " + dt_string + " " + mBCode)

now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Damage Detail table... " + dt_string + " "  + mBCode)
query = pd.read_sql_query("""
    SELECT DISTINCT pdd.[id]
      ,pdd.[promo_damage_id]
      ,pdd.[product_id]
      ,pdd.[quantity]
      ,CONVERT(VARCHAR(23),pdd.[date_changed],121) as [date_changed]
      ,iif(pdd.[disable] = 1,1,0) as [disable]
      -- ,pdd.[quantity2]
    FROM [diQTech_db].[dbo].[diQt_PromoDamageDetail] as pdd
    JOIN [diQTech_db].[dbo].[diQt_PromoDamageBranch] as pdb
    ON pdd.promo_damage_id = pdb.id
    WHERE branch_id = (SELECT top(1) id FROM [diQTech_db].[dbo].diQt_Branch WHERE code = '""" + mBCode + """')""", conn) 
df = pd.DataFrame(query)
mfiledata = mPath +  'mdatapos.PromoDamageDetail.txt'
df.to_csv(mfiledata, index=False, sep='\t')
now = datetime.now()
dt_string = now.strftime("%m-%d-%Y %H:%M:%S")
print("Extracting Promo Damage Detail done... " + dt_string + " " + mBCode)

print("===================================")
conn.close()  # <--- Close the connection
