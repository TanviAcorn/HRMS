<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Document' }}</title>
  <style>
    html, body { height:100%; margin:0; }
    #viewer { min-height:100vh; background:#f6f7f9; }
    canvas.page { display:block; margin:12px auto; box-shadow: 0 1px 2px rgba(0,0,0,0.06); background:#fff; }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
</head>
<body oncontextmenu="return false;">
  <div id="viewer"></div>
  <script>
    const url = @json($streamUrl);
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    (async () => {
      const pdf = await pdfjsLib.getDocument({ url, disableStream: true, disableAutoFetch: true }).promise;
      for (let i = 1; i <= pdf.numPages; i++) {
        const page = await pdf.getPage(i);
        const viewport = page.getViewport({ scale: 1.2 });
        const canvas = document.createElement('canvas');
        canvas.className = 'page';
        const ctx = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        document.getElementById('viewer').appendChild(canvas);
        await page.render({ canvasContext: ctx, viewport }).promise;
      }
    })();

    // Basic deterrents
    document.addEventListener('keydown', (e) => {
      const key = e.key.toLowerCase();
      if ((e.ctrlKey || e.metaKey) && (key === 's' || key === 'p')) e.preventDefault();
    });
  </script>
</body>
</html>
