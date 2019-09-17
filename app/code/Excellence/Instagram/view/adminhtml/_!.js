var config = {
    map:{
        '*' : {
            "owlcarousel" : "Excellence_Instagram/js/owlcarousel/owl.carousel",
            "sliderscript" : "Excellence_Instagram/js/sliderscript"
        }
    },
    shim : {
        "owlcarousel" : {
            deps: ['jquery'],
            export : 'owlcarousel'
        }
        "sliderscript" : {
            deps: ['owlcarousel'],
            export : 'sliderscript'
        }
    }
};