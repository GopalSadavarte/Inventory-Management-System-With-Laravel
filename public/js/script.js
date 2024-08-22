let btn = document.querySelectorAll('.productBtn')
let productForm = document.getElementById('productForm')
let formElements = document.querySelectorAll('.formInputField')
let method = document.getElementById('method')

btn[1].addEventListener('click', e => {
    e.preventDefault()
    window.location.reload()
})
btn[2].addEventListener('click', e => {
    e.preventDefault()
    btn[3].setAttribute('disabled', true)
    btn[4].setAttribute('disabled', true)
    formElements[1].focus()
    for (i = 1; i < formElements.length; i++) {
        if (formElements[i].hasAttribute('disabled'))
            formElements[i].removeAttribute('disabled')
        if (formElements[i].hasAttribute('readonly'))
            formElements[i].removeAttribute('readonly')
    }

    btn[0].removeAttribute('disabled')
})
btn[3].addEventListener('click', e => {
    e.preventDefault()
    btn[2].setAttribute('disabled', true)
    btn[4].setAttribute('disabled', true)
    formElements[0].removeAttribute('readonly')
    if (sessionStorage.key(0) == 'update') sessionStorage.removeItem('update')
    sessionStorage.setItem('delete', 'delete')
    formElements[0].value = ''
    formElements[0].focus()
})
btn[4].addEventListener('click', e => {
    e.preventDefault()
    btn[2].setAttribute('disabled', true)
    btn[3].setAttribute('disabled', true)
    formElements[0].removeAttribute('readonly')

    for (i = 1; i < formElements.length; i++) {
        if (formElements[i].hasAttribute('disabled'))
            formElements[i].removeAttribute('disabled')
        if (formElements[i].hasAttribute('readonly'))
            formElements[i].removeAttribute('readonly')
    }
    if (sessionStorage.key(0) == 'delete') sessionStorage.removeItem('delete')
    sessionStorage.setItem('update', 'update')
    formElements[0].value = ''
    formElements[0].focus()
})

let error = document.getElementById('error-id')

const operation = productId => {
    productForm.setAttribute(
        'action',
        `http://localhost:8000/product/${productId}`
    )
    let input = document.createElement('input')
    input.setAttribute('type', 'hidden')
    input.setAttribute('name', '_method')
    if (sessionStorage.key(0) == 'update') {
        input.setAttribute('value', 'PUT')
    } else {
        input.setAttribute('value', 'DELETE')
    }
    method.append(input)
}

const selectMenu = data => {
    let op = formElements[2].querySelectorAll('option')

    op.forEach(element => {
        if (element.value == data[0].group_no) {
            element.setAttribute('selected', true)
        }
    })

    let op1 = formElements[3].querySelectorAll('option')
    op1.forEach(element => {
        if (element.value == data[0].sub_group_no) {
            element.setAttribute('selected', true)
        }
    })

    let op2 = formElements[9].querySelectorAll('option')
    op2.forEach(element => {
        if (element.value == data[0].GST) {
            element.setAttribute('selected', true)
        }
    })

    let op3 = formElements[10].querySelectorAll('option')
    op3.forEach(element => {
        if (element.value == data[0].GSTOn) {
            element.setAttribute('selected', true)
        }
    })
}
formElements[0].addEventListener('keyup', e => {
    e.preventDefault()
    if (e.key == 'Enter') {
        fetch(`product/${formElements[0].value.trim()}`)
            .then(res => res.json())
            .then(data => {
                if ('error' in data[0]) {
                    error.innerHTML = data[0].error
                    setTimeout(() => {
                        error.innerHTML = ''
                    }, 3000)
                } else {
                    formElements[0].value = data[0].product_id
                    formElements[1].value = data[0].product_name
                    formElements[5].value = data[0].weight
                    formElements[6].value = data[0].rate
                    formElements[7].value = data[0].MRP
                    formElements[8].value = data[0].discount
                    selectMenu(data)
                    operation(data[0].id)
                    btn[0].removeAttribute('disabled')
                    formElements[0].setAttribute('readonly', true)
                }
            })
    }
})

let productInfo = document.getElementById('product-container')
let productTable = document.getElementById('productTable')
let closeBtn = document.querySelector(
    '#product-container div div #product-close-img'
)
let selectBtn = document.getElementById('product-select-button')
let tableRow = productTable.querySelectorAll('.tableRow')
formElements[0].addEventListener('keyup', e => {
    e.preventDefault()
    if (e.key == 'q' && e.ctrlKey) {
        productInfo.classList.remove('d-none')
    }
})

closeBtn.addEventListener('click', e => {
    e.preventDefault()
    productInfo.classList.add('d-none')
})

tableRow.forEach(tr => {
    tr.addEventListener('click', e => {
        tableRow.forEach(hideRow => {
            hideRow.classList.remove('text-light', 'bg-primary')
            tr.classList.add('text-light', 'bg-primary')
            hideRow.removeAttribute('id')
            tr.setAttribute('id', 'selectedRow')
            selectBtn.removeAttribute('disabled')
        })
    })
})

selectBtn.addEventListener('click', e => {
    e.preventDefault()
    let row = document.getElementById('selectedRow')
    operation(row.firstElementChild.textContent)
    formElements[0].value = row.children[1].textContent
    formElements[1].value = row.children[4].textContent
    formElements[5].value = row.children[5].textContent
    formElements[6].value = row.children[6].textContent
    formElements[7].value = row.children[7].textContent
    formElements[8].value = row.children[8].textContent

    let data = [
        {
            group_no: row.children[2].textContent,
            sub_group_no: row.children[3].textContent,
            GST: row.children[9].textContent,
            GSTOn: row.children[10].textContent
        }
    ]

    selectMenu(data)
    btn[0].removeAttribute('disabled')
    formElements[0].setAttribute('readonly', true)

    closeBtn.click()
})
document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
