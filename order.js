const products = {
    "TYPE 10" :{
        "power": "10 KVa / 8 KW",
        "phase": "single phase",
        "output": "220Volts",
        "amps": "25amp",
        "size": "122 x 61 x 92 / 150 kg"
    },
    "TYPE 17" :{
        "power": "10 KVa / 8 KW",
        "phase": "single phase",
        "output": "220Volts",
        "amps": "25amp",
        "size": "122 x 61 x 92 / 150 kg"
    }
    
}

document.addEventListener('DOMContentLoaded', ()=>{
    const inpElem = document.querySelector('#inp_products');
    inpElem.innerHTML = '';
    //loop through products
    for(let product in products){
        inpElem.innerHTML += `<option value="${product}">${product}</option>`;
    }
})