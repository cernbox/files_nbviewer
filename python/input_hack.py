# Current nbconvert module does not support input from stdin.
# This scripts allow to use nbconvert with notebooks passed through standard input
# Other workaround would be to temporary copy the notebook from eos to the local disk
# and convert it

import nbformat
from nbconvert import HTMLExporter
import sys

#fileContent = sys.argv[1]

fileContent = ''
for line in sys.stdin:
	fileContent = u''.join((fileContent, unicode(line, 'utf-8')))

tnb = nbformat.reads(fileContent, as_version=4)

html_exporter = HTMLExporter()
html_exporter.template_file = 'full' # basic html to be embeded in CERNBox

(body, resources) = html_exporter.from_notebook_node(tnb)

print(body.encode('ascii', 'ignore'))
