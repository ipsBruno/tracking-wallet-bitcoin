# tracking-wallet-bitcoin


A simple script to get blockchain.info blocks info and clusterize address to parse wallets by user


# Server Info
Folder Server

application/config/database.php

Config the database


application/controllers/welcome.php

The simple script code

# Mysql Info

**Database:** 

rastreio

**Tables**

wallet (indexed columns: id int32 autoincrement)

adresses (indexed columns: addr varchar 64, wallet int32)




# Cliente Info

Folder Cliente
npm install request --save
node index.js

The crawler init in block 515.000 to 0 and this take several time and storage to sincronize with blockchain info.



# Similar Services
https://www.walletexplorer.com





