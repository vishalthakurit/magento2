echo "--------Upgrading-------------";
php bin/magento setup:upgrade
echo "--------Deploy-------------";
php bin/magento setup:static-content:deploy -f
echo "--------Compile-------------";
php bin/magento setup:di:compile
echo "--------Indexing-------------";
php bin/magento indexer:reindex
echo "--------Cache- Clean & Flush-------------";
php bin/magento cache:clean
php bin/magento cache:flush
echo "--------Permission-------------";
chmod -R 777 var/ pub/ generated/ app/