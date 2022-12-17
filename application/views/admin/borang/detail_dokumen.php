<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
		integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous" />
	<link rel="stylesheet" href="<?= base_url() ?>/public/plugins/viewer-css/main.css" />
	<title>PDF Viewer</title>
</head>

<body>
	<div class="top-bar">
		<button class="btn" id="prev-page">
			<i class="fas fa-arrow-circle-left"></i> Prev Page
		</button>
		<button class="btn" id="next-page">
			Next Page <i class="fas fa-arrow-circle-right"></i>
        </button>
		<span class="page-info">
			Page <span id="page-num"></span> of <span id="page-count"></span>
		</span>
	</div>

	<canvas id="pdf-render"></canvas>

	<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
    <script src="js/main.js"></script>
    
    <!-- RENDER SCRIPT -->
	<script>

		const url = '<?= base_url() ?><?= $dokumen['file'] ?>';

		let pdfDoc = null,
			pageNum = 1,
			pageIsRendering = false,
			pageNumIsPending = null;

		const scale = 1.2,
			canvas = document.querySelector('#pdf-render'),
			ctx = canvas.getContext('2d');

		// Render the page
		const renderPage = num => {
			pageIsRendering = true;

			// Get page
			pdfDoc.getPage(num).then(page => {
				// Set scale
				const viewport = page.getViewport({
					scale
				});
				canvas.height = viewport.height;
				canvas.width = viewport.width;

				const renderCtx = {
					canvasContext: ctx,
					viewport
				};

				page.render(renderCtx).promise.then(() => {
					pageIsRendering = false;

					if (pageNumIsPending !== null) {
						renderPage(pageNumIsPending);
						pageNumIsPending = null;
					}
				});

				// Output current page
				document.querySelector('#page-num').textContent = num;
			});
		};

		// Check for pages rendering
		const queueRenderPage = num => {
			if (pageIsRendering) {
				pageNumIsPending = num;
			} else {
				renderPage(num);
			}
		};

		// Show Prev Page
		const showPrevPage = () => {
			if (pageNum <= 1) {
				return;
			}
			pageNum--;
			queueRenderPage(pageNum);
		};

		// Show Next Page
		const showNextPage = () => {
			if (pageNum >= pdfDoc.numPages) {
				return;
			}
			pageNum++;
			queueRenderPage(pageNum);
		};

		// Get Document
		pdfjsLib
			.getDocument(url)
			.promise.then(pdfDoc_ => {
				pdfDoc = pdfDoc_;

				document.querySelector('#page-count').textContent = pdfDoc.numPages;

				renderPage(pageNum);
			})
			.catch(err => {
				// Display error
				const div = document.createElement('div');
				div.className = 'error';
				div.appendChild(document.createTextNode(err.message));
				document.querySelector('body').insertBefore(div, canvas);
				// Remove top bar
				document.querySelector('.top-bar').style.display = 'none';
			});

		// Button Events
		document.querySelector('#prev-page').addEventListener('click', showPrevPage);
		document.querySelector('#next-page').addEventListener('click', showNextPage);

    </script>
    
</body>

</html>
