const fs = require('fs');
const path = 'resources/views/pages/user-master.blade.php';
const text = fs.readFileSync(path, 'utf8');
const matched = text.match(/<script>[\s\S]*?<\/script>/);
if (!matched) {
  console.log('NOSCRIPT');
  process.exit(1);
}
let js = matched[0].replace(/^<script>/, '').replace(/<\/script>$/, '');
js = js.replace(/@json\([^\)]*\)/g, 'null');
js = js.replace(/\{\{[^\}]*\}\}/g, '""');
try {
  new Function(js);
  console.log('SYNTAX_OK');
} catch (e) {
  console.error('ERROR', e.message);
  console.error(e.stack);
  process.exit(1);
}
