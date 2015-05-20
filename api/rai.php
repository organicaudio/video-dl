<!--
: "
-->
<?php
if(isset($_GET['url'])) {
    $file = __FILE__;
    $url = ($_GET["url"]);
    $quality = ($_GET["q"]);
    $param = ($_GET["p"]);
    if(!isset($_GET['q'])) {
        $quality = dontmindme;
    };
    $cmd =  "bash " .  escapeshellarg($file) .  ' ' . escapeshellarg($url) .  ' ' .  escapeshellarg($quality) .  ' ' . escapeshellarg($param);
    $message = shell_exec("$cmd");
    print_r($message);
}
?>
<!--
"
# Rai.TV download script
# Created by Daniil Gentili (http://daniil.eu.org)
# This program is licensed under the GPLv3 license.
# Web version: can be incorporated in websites.
[ "$*" = "" ] && exit 1
function kill() {
echo "<center><h1><a>Questo non &#232; un indirizzo Rai.</a></h1></center>"; exit 1
}

dl=$(echo $1 | grep -q http: && echo $1 || echo http:$1)

curl -w "%{url_effective}\n" -L -s -I -S $dl -o /dev/null  | grep -qE 'http://www.*.rai..*/dl/RaiTV/programmi/media/.*|http://www.*.rai..*/dl/RaiTV/tematiche/*|http://www.*.rai..*/dl/.*PublishingBlock-.*|http://www.*.rai..*/dl/replaytv/replaytv.html.*|http://.*.rai.it/.*|http://www.rainews.it/dl/rainews/.*|http://mediapolisvod.rai.it/.*|http://*.akamaihd.net/*' || kill


# OK, here we have all the functions.

function error() {
echo "$URLS" | awk 'END {print $NF}'
}

function var() {
eval $*
}

size() {
echo `echo $1 | awk -F. '$0=$NF'`, $(
tmpsize=$(echo "$size" | sed "$(echo "$URLS" | grep -n "$1" | cut -f1 -d:)!d")

if [ "$tmpsize" != "" ]; then echo ""$tmpsize", "; fi)$(mplayer -vo null -ao null -identify -frames 0 $1 2>/dev/null | grep kbps | awk '{print $3}')
}

getsize() {
info=$(echo "$unformatted" | grep "$a" | sed 's/http.*//')
}

formatoutput() {

urlsfromunformatted="$(echo "$unformatted" | awk 'NF>1{print $NF}')"

four="$(echo "$urlsfromunformatted" | grep .*_400.mp4)"
six="$(echo "$urlsfromunformatted" | grep .*_600.mp4)"
eight="$(echo "$urlsfromunformatted" | grep .*_800.mp4)"
twelve="$(echo "$urlsfromunformatted" | grep .*_1200.mp4)"
fifteen="$(echo "$urlsfromunformatted" | grep .*_1500.mp4)"
eighteen="$(echo "$urlsfromunformatted" | grep .*_1800.mp4)"

normal="$(echo "$urlsfromunformatted" | grep -v .*_400.mp4 | grep -v .*_600.mp4 | grep -v .*_800.mp4 | grep -v .*_1200.mp4 | grep -v .*_1500.mp4 | grep -v .*_1800.mp4)"

formats="$(
[ "$four" != "" ] && for a in $four; do getsize
 echo "Minimum quality $info $a";done


[ "$six" != "" ] && for a in $six; do getsize
 
 echo "Low quality $info $a";done



[ "$eight" != "" ] && for a in $eight; do getsize

 echo "Medium-low quality $info $a";done


[ "$twelve" != "" ] && for a in $twelve; do getsize

 echo "Medium quality $info $a";done


[ "$fifteen" != "" ] && for a in $fifteen; do getsize

 echo "Medium-high quality $info $a";done


[ "$eighteen" != "" ] && for a in $eighteen; do getsize

 echo "Highest quality $info $a";done


[ "$normal" != "" ] && for a in $normal; do getsize

 echo "Normal quality $info $a";done

)"
formats="$(echo "$formats" | awk '{print NR, $0}')"
}


function checkurl() {
tbase="$base"
base=

tbase="$(echo "$tbase" | sort | awk '!x[$0]++')"


for u in "$tbase";do wget -S --tries=3 --spider $u 2>&1 | grep -q 'HTTP/1.1 200 OK' && base="$base
$u"; done

}

# Relinker function



function relinker_rai() {
# Get a working url


for f in `echo $* | awk '{ while(++i<=NF) printf (!a[$i]++) ? $i FS : ""; i=split("",a); print "" }'`; do
 
 dl=$(echo $f | grep -q http: && echo $1 || echo http:$1)


 url="$(wget "$dl&output=43" -q -O -)"
 base=$(echo "$url" | sed 's/<\/url>/\
&/g' | sed 's/.*<url>//' | grep -E '.*.mp4$|.*.wmv$')
 
 
 checkurl
 
 if [ "$base" = "" ]; then
  tmpurl="$(wget "$dl&output=4" -q -O -)"

  url=$(echo "$tmpurl" | grep -q creativemedia && echo "$tmpurl" || curl -w "%{url_effective}\n" -L -s -I -S $dl -A "" -o /dev/null)
 
  server="$(echo "$url" | sed 's/http:\/\///' | sed 's/\/.*//')"
 
 
  m3u8=$(wget "$dl&output=25" -q -O - | sed 's/.*<url>//' | sed 's/<\/url>.*//' | grep http | sed 's/.*\/i//' | sed '0,/\,/s//\{/' | sed -r 's/,([^,]*)$/\}\1/')

  ext=$(echo $m3u8 | sed 's/.*\}\.//' | sed 's/\..*//' | sed 's\/.*//')
  m3u8="$(echo $m3u8 | sed 's/\}.*//')"

  base="$(eval echo http://$server/$m3u8}.$ext)"

  checkurl
 fi
 
 if [ "$base" = "" ]; then
  url="$(wget "$dl&output=4" -q -O -)"
  base=$(echo "$url" | grep -q creativemedia && echo "$url" || curl -w "%{url_effective}\n" -L -s -I -S $dl -A "" -o /dev/null)
 fi
 
 checkurl

 TMPURLS="$TMPURLS
$base"
done

base="$TMPURLS"

ext=$(echo $base | awk -F. '$0=$NF')

for t in _400.$ext _600.$ext _800.$ext _1200.$ext _1500.$ext _1800.$ext; do for i in _400.$ext _600.$ext _800.$ext _1200.$ext _1500.$ext _1800.$ext; do tbase="$tbase
$(echo "$base" | sed "s/$t/$i/")"; done ;done

tmpbase="$(echo "$tbase" | sort | awk '!x[$0]++')"
base=


for i in $tmpbase;do tmpwget="$(wget -S --spider $i 2>&1)"; echo "$tmpwget" | grep -q '200 OK' && URLS="$URLS
$i" && size="$size
$(echo "$tmpwget" | grep -E '^Length|^Lunghezza' | sed 's/.*(//' | sed 's/).*//')B"; done




[[ -z $title ]] && todl=$(echo $URLS | sed 's/.*\///') || todl=$(echo $title.$(echo $URLS | awk -F. '$0=$NF'))

# Quality checks

unformatted="$([ "$URLS" != "" ] && for a in $URLS; do echo "(`size $a`) $a";done)"

echo "$userinput
$title $videoTitolo
$unformatted
endofdbentry" >> /var/www/rai-db.txt

formatoutput

}


function rai() {
# Store the page in a variable
file=$(wget $1 -q -O -)

echo $1 | grep -q http://www.*.rai..*/dl/replaytv/replaytv.html.*
# Get the relinkers

if [ "$?" = "0" ]; then 
 # Rai replay
 v=$(echo $1 | sed 's/.*v=//' | sed 's/\&.*//')

 day=$(echo $1 | sed 's/.*?day=//' | sed 's/\&.*//' | tr -s "-" "_")

 tmpch=$(echo $1 | sed 's/.*ch=//' | sed 's/\&.*//')

 let "vprev = $v - 1"

 ch=$([ "$tmpch" = "1" ] && echo RaiUno; [ "$tmpch" = "2" ] && echo RaiDue; [ "$tmpch" = "3" ] && echo RaiTre; [ "$tmpch" = "31" ] && echo RaiCinque; [ "$tmpch" = "32" ] && echo RaiPremium; [ "$tmpch" = "23" ] && echo RaiGulp; [ "$tmpch" = "38" ] && echo RaiYoyo)

 json=$(wget http://www.rai.it/dl/portale/html/palinsesti/replaytv/static/"$ch"_$day.html -q -O -)

 echo "$json" | grep -q $vprev

 tmpjson="$(if [ "$?" = 0 ]; then echo "$json" |
sed -n "1,/$v/p" |
awk "/$vprev/{i++}i" |
awk '/{/{i++}i' |
tr -s "," "\n" |
tr -s '"' "\n" |
sed 's/\\//g'; else echo "$json" |
sed -n "1,/$v/p" |
awk '/{/{i++}i' |
tr -s "," "\n" |
tr -s '"' "\n" |
sed 's/\\//g';fi)"

 replay=$(echo "$tmpjson" | sed 's/.*://' | grep mediapolis | sort | awk '!x[$0]++')

 # Get the title
 videoTitolo=$(echo "$tmpjson" | grep -A 2 '^t$' | awk 'END{print}')
 title="${videoTitolo//[^a-zA-Z0-9 ]/}"
 title=`echo $title | tr -s " "`
 title=${title// /_}

 relinker_rai $replay

else

 echo "$file" | grep -q videoURL

 if [ "$?" != "0" ]; then
  eval $(echo "$file" | grep 'content="ContentItem' | cut -d" " -f2) &&
  file="$(wget http://www.rai.it/dl/RaiTV/programmi/media/"$content".html -q -O -)"
 fi

 # Get the video URLs

 eval "$(echo "$file" | grep videoURL | sed "s/var//g" | tr -d '[[:space:]]')"

 # Get the title
 $(echo "$file" | grep videoTitolo)
 title="${videoTitolo//[^a-zA-Z0-9 ]/}"
 title=`echo $title | tr -s " "`
 title=${title// /_}

 # Get the destination URL.
 set +u
 relinker_rai $videoURL_MP4 $videoURL_H264 $videoURL_WMV $videoURL
 set +u
fi
}

rai_db() {
db="$(sed -n '/'"$saneuserinput"'/,$p' /var/www/rai-db.txt | sed -n '/endofdbentry/q;p' | sed '1d')"

titles="$(echo "$db" | sed -n 1p)"

unformatted="$(echo "$db" | sed '1d')"

title="$(echo "$titles" | cut -d \  -f 1)"

videoTitolo="$(echo "$titles" | cut -d' ' -f2-)"

formatoutput
}

# And here we have the final URL check and the working part.

second=$2
third=$3

userinput="$dl"
saneuserinput=$(echo "$dl" | sed 's/\//\\\//g' | sed 's/\&/\\\&/g')


grep -q "$dl" /var/www/rai-db.txt

if [ "$?" = 0 ]; then
 rai_db
 [ "$formats" = "" ] && exit || echo "$title $videoTitolo
$formats"

else
 curl -w "%{url_effective}\n" -L -s -S $dl -o /dev/null  | grep -qE 'http://www.*.rai..*/dl/RaiTV/programmi/media/.*|http://www.*.rai..*/dl/RaiTV/tematiche/*|http://www.*.rai..*/dl/.*PublishingBlock-.*|http://www.*.rai..*/dl/replaytv/replaytv.html.*|http://.*.rai.it/.*|http://www.rainews.it/dl/rainews/.*' && rai $dl $2 $3 || relinker_rai $dl $2 $3
 [ "$formats" = "" ] && exit || echo "$title $videoTitolo
$formats"

fi
# A bit messed up, I know. But at least it works (right?).
-->