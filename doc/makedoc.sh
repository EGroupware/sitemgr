# this scripts helps make the documentation in html ps and pds
# the sed scripts is there to counter a bug in docbook export of Lyx
set -x
#mv sitemgr.sgml sitemgr.sgml.bak
#sed "s/<\/listitem><\/listitem>/<\/listitem>/" sitemgr.sgml.bak >sitemgr.sgml
db2html --nochunks sitemgr.sgml | sed 's/&lcub;/<b>/
s/&rcub;/<\/b>/' > sitemgr.html
rm -rf sitemgr
exit
db2dvi sitemgr.sgml
dvips -o sitemgr.ps sitemgr.dvi
ps2pdf sitemgr.ps
