function bufferToBase64(buf) {
    let binstr = Array.prototype.map.call(buf, function (ch) {
        return String.fromCharCode(ch);
    }).join('');
    return btoa(binstr);
}
//
// async function addImgToPdf(filePath, imgUrl, imgParams) {
//     const { PDFDocument, StandardFonts, grayscale, rgb, degrees } = PDFLib;
//     const existingPdfBytes = await fetch(filePath).then(res => res.arrayBuffer());
//
//     const pdfDoc = await PDFDocument.load(existingPdfBytes);
//     const pages = pdfDoc.getPages();
//     const firstPage = pages[0];
//
//     const pngImageBytes = await fetch(imgUrl).then((res) => res.arrayBuffer());
//     const pngImage = await pdfDoc.embedPng(pngImageBytes);
//
//     firstPage.drawImage(pngImage, imgParams);
//
//     const pdfBytes = await pdfDoc.save();
//
//     let base64 = bufferToBase64(pdfBytes);
//
//     //download(pdfBytes, "pdf-lib_modify_example.pdf", "application/pdf");
//
//     $.ajax({
//         url: '/ulab/file/bytePdfToServerAjax',
//         data: {
//             file: base64, path: filePath
//         },
//         method: 'POST',
//         success: function (json) {
//             let arr = JSON.parse(json);
//             console.log(arr)
//         },
//         error: function (jqXHR, exception) {
//             let msg = '';
//             if (jqXHR.status === 0) {
//                 msg = 'Not connect.\n Verify Network.';
//             } else if (jqXHR.status === 404) {
//                 msg = 'Requested page not found. [404]';
//             } else if (jqXHR.status === 500) {
//                 msg = 'Internal Server Error [500].';
//             } else if (exception === 'parsererror') {
//                 msg = 'Requested JSON parse failed.';
//             } else if (exception === 'timeout') {
//                 msg = 'Time out error.';
//             } else if (exception === 'abort') {
//                 msg = 'Ajax request aborted.';
//             } else {
//                 msg = 'Uncaught Error.\n' + jqXHR.responseText;
//             }
//             console.error(msg)
//         }
//     })
// }