if(VirtualATCEnable == true){
@if($user->enable)
    let data = JSON.parse(`{!! json_encode($user->data) !!}`)
    document.querySelectorAll(".virtual-atc-block").forEach(function(elm){
        let product_id = elm.getAttribute("data-product-id");
        let enable = elm.getAttribute("data-enable");
        if(enable == "true"){
            let people = sessionStorage.getItem(product_id);
            if(!people){
                let gte = parseInt(data.gte);
                let lte = parseInt(data.lte);
                people = Math.floor(gte + Math.random()*(lte + 1 - gte));
                sessionStorage.setItem(product_id, people);
            }
            let str = data.customize.replace(`{number}`, people);
            let html = `<span style="color: ${data.color}">${str}</span>`
            elm.innerHTML = html;
        }
    });
@endif
}