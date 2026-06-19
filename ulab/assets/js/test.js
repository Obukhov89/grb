var editor = new wysihtml5.Editor("textarea", {
    toolbar:        "toolbar",
    parserRules:    wysihtml5ParserRules,
    useLineBreaks:  false
});

Dropzone.options.dropzoneExample = {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 2, // MB
    url: "/ulab/index/uploadTest/",
    // accept: function(file, done) {
    //     console.log('accept')
    // },
    init: function() {
        console.log('init')

        this.on("success", function(file, responseText) {
            console.log('success: ' + responseText);
        })

        this.on("sending", function(file, xhr, formData) {
            console.log('sending', file, xhr, formData)
        })

        this.on("addedfile", file => {
            console.log("A file has been added");
        })
    },
    error: function (file, message) {
        console.log('error: ' + message)
    }
}


