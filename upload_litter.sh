response=$(curl -s -F "reqtype=fileupload" -F "time=12h" -F "fileToUpload=@/tmp/blocksy-child-compare.zip" https://litterbox.catbox.moe/user/api.php)
echo "Litterbox URL: $response"
