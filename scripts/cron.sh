#!/bin/bash
TOOL_HOME=$(dirname "$(readlink -f "$0")")
TOOL_HOME=$(dirname "$TOOL_HOME")
outputPath=$(php "$TOOL_HOME/scripts/env.php" XAPI_PROFILE_PUBLIC_SITE)

OLD_IFS=$IFS
IFS=
find "$TOOL_HOME/cron" -name '*.profile' -print0 | xargs -r0 stat --printf='%y %n\0' | python -c 'import sys; cut = lambda s : s.split(" "); extract=lambda l: l[3] if len(l) >= 3 else ""; sys.stdout.write("\0".join(map(extract, map(cut, sys.stdin.read().split("\0")))))' |  while read -r -d $'\0' p; do 
githubURL=$(head -n 1 "$p")
profilePath=$(head -n 2 "$p" | tail -n +2)
profileName=$(basename "$profilePath")
profileName=${profileName%.*}
gitSHA=$(basename "$p")
gitSHA=${gitSHA%.*}

wd=$(mktemp -d)
cd $wd
git clone "$githubURL" . > /dev/null 2>&1
git checkout "$gitSHA" > /dev/null 2>&1
php "$TOOL_HOME/scripts/profile2html.php" -i "$profilePath" > "$outputPath/$profileName.html"
cp "$profilePath" "$outputPath"
cd $TOOL_HOME
rm -fr "$wd"
rm -fr "$p"

done
IFS=$OLD_IFS
