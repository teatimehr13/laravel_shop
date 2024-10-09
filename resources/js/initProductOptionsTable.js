//待改成vue3的形式來寫這裡的前端



let counter = 0;
const appendItem = (item, currentData = null, container) => {
    let newItem = item.cloneNode(true);
    newItem.style.display = '';
    newItem.style.paddingBottom = '60px';
    let inputs = newItem.querySelectorAll('input');

    let objectName = `new_${counter}`;

    if(currentData){
        objectName = currentData.id;
    }else{
        counter++
    }

    for(const input of inputs){
        input.disabled = false;
        let name = input.getAttribute('attr-name');
        input.setAttribute('name', `product_options[${objectName}][${name}]`);

        if(currentData){
            let value = currentData[name];
            if(['text', 'number'].includes(input.type)){
                input.value = value
            }

            if(input.type == 'checkbox'){
                input.checked = value;
            }

            if(name == 'image'){
                input.closest('div').querySelector('img').src = value;
            }
        }
    }

    let deleteButton = newItem.querySelector('button');

    deleteButton.addEventListener('click', function(e){
        let btn = e.target;            
        let selector = btn.getAttribute('data-target');
        btn.closest(selector).outerHTML = '';
    })
    container.appendChild(newItem);
}

//同時做了頁面的初始渲染及add按鈕觸發事件
const initProductOptionsTable = (
    add_button,
    container,
    item,
    product_options_json
) => {
    const product_options = JSON.parse(product_options_json);

    for(const product_option of product_options){     
        //item為寫好被隱藏的內容，product_option為資料，container為被渲染的dom   
        appendItem(item, product_option, container)
    }
    
    //按鈕新增觸發事件
    add_button.addEventListener('click', function(){
        let newItem = item.cloneNode(true);
        newItem.style.display = '';
        newItem.style.paddingBottom = '60px';
        let inputs = newItem.querySelectorAll('input');

        let objectName = `new_${counter}`;
        counter++

        for(const input of inputs){
            input.disabled = false;
            let name = input.getAttribute('attr-name');
            input.setAttribute('name', `product_options[${objectName}][${name}]`);
        }

        let deleteButton = newItem.querySelector('button');
        deleteButton.addEventListener('click', function(e){
            let btn = e.target;            
            let selector = btn.getAttribute('data-target');
            btn.closest(selector).outerHTML = '';
        })

        container.appendChild(newItem);
    })
      
}

export default initProductOptionsTable