# this scripts helps make the documentation in html ps and pds
# the sed scripts is there to counter a bug in docbook export of Lyx
set -x
mv modules.sgml modules.sgml.bak
sed "s/<\/listitem><\/listitem>/<\/listitem>/" modules.sgml.bak >modules.sgml
db2html -u modules.sgml
mv modules/modules.html .
rm -rf modules
db2dvi modules.sgml
dvips -o modules.ps modules.dvi
ps2pdf modules.ps
