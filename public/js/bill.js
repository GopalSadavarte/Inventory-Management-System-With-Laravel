import { manageProductInfo } from './product.js'
//bill generate activation start here
let addBtn = document.getElementById('addBtn')
let billTable = document.getElementById('bill-table')
let addProductForm = document.getElementById('addProductForBill')
let payableAmount = document.getElementById('payableAmount')
let paidAmount = document.getElementById('paidAmount')
let total = document.getElementById('total')
let totalDiscountField = document.getElementById('totalDiscount')
let totalDiscount = 0
let quantity = document.getElementById('qty')
let inventoryId = document.getElementById('inventoryId')
let inputField = document.querySelectorAll('#addProductForBill div input')
let d
addBtn.addEventListener('click', e => {
    e.preventDefault()
    let sum = 0
    d = 0
    //get entered product data from form

    let formData = new FormData(addProductForm)
    let pId = formData.get('pId')
    // let pName = formData.get('pName')
    // let mrp = formData.get('mrp')
    let qty = formData.get('qty')
    let rate = formData.get('rate')
    let disc = formData.get('discount')
    let formElementKeys = formData.keys()
    //generating dynamic elements in the bill table
    let tr = document.createElement('tr')
    tr.classList.add('bill-row')
    formElementKeys.forEach(e => {
        let td = document.createElement('td')
        let input = document.createElement('input')
        input.setAttribute('class', 'form-control')
        input.setAttribute('value', formData.get(e))
        if (e == 'productPId') {
            input.setAttribute('name', 'productPId[]')
            td.classList.add('d-none')
        }
        if (e == 'pName') {
            input.setAttribute('readonly', true)
            input.setAttribute('name', 'productName[]')
        }
        if (e == 'pId') {
            input.classList.add('product-id')
            input.setAttribute('name', 'productId[]')
            input.setAttribute('readonly', true)
        }

        if (e == 'qty' || e == 'rate' || e == 'discount' || e == 'mrp') {
            td.classList.add('editable-field')
            if (e == 'qty') {
                let max = sessionStorage.getItem('productQty')
                input.setAttribute('maxlength', max.length)
                input.classList.add('quantity-edit')
                sessionStorage.removeItem('productQty')
            }
        }

        if (e == 'inventoryId') {
            input.value = formData.get(e)
            input.setAttribute('name', 'inventId[]')
            td.classList.add('d-none')
        }

        if (e == 'max-qty') {
            td.classList.add('d-none')
            input.value = formData.get(e)
            input.classList.add('max-qty')
        }

        if (e == 'rate') {
            input.setAttribute('name', 'rate[]')
        }
        if (e == 'mrp') {
            input.setAttribute('name', 'mrp[]')
        }

        if (e == 'qty') {
            input.setAttribute('name', 'qty[]')
        }

        if (e == 'discount') {
            input.setAttribute('name', 'discount[]')
            input.classList.add('discount-field')
        }

        tr.append(td)
        td.append(input)
        billTable.append(tr)
    })
    //generating last net amount table cell
    let td = document.createElement('td')
    let input = document.createElement('input')
    input.setAttribute('class', 'form-control')
    input.setAttribute('readonly', true)
    input.setAttribute('name', 'netAmount[]')
    input.classList.add('netAmount')
    d = (qty * rate * disc) / 100
    input.value = (qty * rate - (qty * rate * disc) / 100).toFixed(2)
    tr.append(td)
    td.append(input)
    billTable.append(tr)

    //getting sum of all net amounts and print in total amount field
    document.querySelectorAll('.netAmount').forEach(e => {
        sum = sum + parseFloat(e.value)
    })
    totalDiscount = totalDiscount + d
    totalDiscountField.value = totalDiscount.toFixed(0)
    total.value = sum.toFixed(0)
    payableAmount.value = sum.toFixed(0)
    addProductForm.reset()
    addProductForm.firstElementChild.children[1].focus()
    sessionStorage.removeItem('productQty')
    quantity.removeAttribute('maxlength')
    inputField[1].focus()
    addBtn.setAttribute('disabled', true)
})

//Adding the javascript events in the dynamically generated input fields
let observe = new MutationObserver(() => {
    let editableFields = document.querySelectorAll('.editable-field')
    let billTableRows = document.querySelectorAll('#bill-table .bill-row')
    let netAmount = document.querySelectorAll('.netAmount')
    let quantityField = document.querySelectorAll('.quantity-edit')
    let discountEditableFields = document.querySelectorAll(
        '#bill-table .discount-field'
    )
    let flag = 0
    netAmount.forEach(e => {
        if (e.value != 0 && e.value != '') flag = 1
    })
    if (billTableRows.length > 0 && flag == 1)
        document.getElementById('submit').removeAttribute('disabled')
    else document.getElementById('submit').setAttribute('disabled', true)
    const eventHandlerOfEditableFields = (event, e) => {
        event.preventDefault()
        //Enter key event to recalculate new total after adit
        if (event.key == 'Enter') {
            let qty = parseInt(e.parentElement.children[3].children[0].value)
            let rate = parseFloat(e.parentElement.children[4].children[0].value)
            let disc = parseFloat(e.parentElement.children[6].children[0].value)

            if (isNaN(disc)) disc = 0
            if (isNaN(rate)) rate = 0
            if (isNaN(qty)) qty = 0

            let v = qty * rate - (qty * rate * disc) / 100
            e.parentElement.children[9].children[0].value = v.toFixed(2)
            let sum = 0
            document.querySelectorAll('.netAmount').forEach(e => {
                sum = sum + parseFloat(e.value)
            })
            total.value = sum.toFixed(0)
            payableAmount.value = sum.toFixed(0)
            calculateDiscount()
        }

        //creating ctrl+q shortcut to remove table element
        if (event.ctrlKey == true && event.key == 'q') {
            e.parentElement.remove()
            let sum = 0
            document.querySelectorAll('.netAmount').forEach(e => {
                sum = sum + parseFloat(e.value)
            })
            total.value = sum.toFixed(0)
            payableAmount.value = sum.toFixed(0)
            calculateDiscount()
        }
    }

    const mouseMoveHandler = e => {
        let qty = parseInt(e.parentElement.children[3].children[0].value)
        let rate = parseFloat(e.parentElement.children[4].children[0].value)
        let disc = parseFloat(e.parentElement.children[6].children[0].value)

        if (isNaN(disc)) disc = 0
        if (isNaN(rate)) rate = 0
        if (isNaN(qty)) qty = 0
        let v = qty * rate - (qty * rate * disc) / 100
        e.parentElement.children[9].children[0].value = v.toFixed(2)
        let sum = 0
        document.querySelectorAll('.netAmount').forEach(e => {
            sum = sum + parseFloat(e.value)
        })
        total.value = sum.toFixed(0)
        payableAmount.value = sum.toFixed(0)
        calculateDiscount()
    }

    const calculateDiscount = () => {
        totalDiscount = 0
        let billTableRows = document.querySelectorAll('#bill-table .bill-row')
        billTableRows.forEach(tr => {
            let qty = parseInt(tr.children[3].children[0].value)
            let rate = parseFloat(tr.children[4].children[0].value)
            let disc = parseFloat(tr.children[6].children[0].value)

            if (isNaN(disc)) disc = 0
            if (isNaN(rate)) rate = 0
            if (isNaN(qty)) qty = 0

            d = (qty * rate * disc) / 100
            totalDiscount = totalDiscount + d
        })

        totalDiscountField.value = totalDiscount.toFixed(0)
    }

    editableFields.forEach(e => {
        e.firstElementChild.addEventListener('keyup', event => {
            eventHandlerOfEditableFields(event, e)
        })
        e.firstElementChild.addEventListener('mousemove', event => {
            event.preventDefault()
            mouseMoveHandler(e)
        })
    })

    discountEditableFields.forEach(e => {
        e.addEventListener('keyup', event => {
            event.preventDefault()
            if (event.key == 'Enter') {
                calculateDiscount()
            }
        })
        e.addEventListener('mousemove', event => {
            event.preventDefault()
            calculateDiscount()
        })
    })

    quantityField.forEach(e => {
        e.addEventListener('keyup', event => {
            event.preventDefault()
            let inStock = parseInt(
                e.parentElement.parentElement.children[8].children[0].value
            )
            e.setAttribute(
                'maxlength',
                e.value.includes('-')
                    ? inStock.toString().length + 1
                    : inStock.toString().length
            )
            let fieldValue = e.value.includes('-') ? e.value : parseInt(e.value)
            if (fieldValue > inStock && typeof fieldValue == 'number') {
                e.value = inStock
            }
        })
    })
})
//configure the MutationObserver object
observe.observe(billTable, { childList: true })

//calculating return amount from paid and payable amount
paidAmount.addEventListener('keyup', e => {
    e.preventDefault()
    if (e.key == 'Enter') {
        document.getElementById('returnAmount').value =
            parseInt(paidAmount.value) - parseInt(payableAmount.value)
    }
})

//displaying the customer container on entering the keyboard shortcut ctrl+q on customer name field
let customerName = document.getElementById('customerName')
let customerCloseImg = document.getElementById('customer-close-img')
let customerContainer = document.getElementById('customer-container')
customerName.addEventListener('keyup', e => {
    if (e.ctrlKey && e.key == 'q') {
        customerContainer.classList.remove('d-none')
    }
})

customerCloseImg.addEventListener('click', () => {
    customerContainer.classList.add('d-none')
})

let customerTableRows = document.querySelectorAll('#customerTable .tableRow')
let customerSelectBtn = document.getElementById('customer-select-button')
let customerFormInputFields = document.querySelectorAll(
    '#customerSelectForm div div input'
)

customerTableRows.forEach(tr => {
    tr.addEventListener('click', () => {
        customerTableRows.forEach(hideRow => {
            hideRow.classList.remove('bg-primary', 'text-light')
            tr.classList.add('bg-primary', 'text-light')
            hideRow.removeAttribute('id')
            tr.setAttribute('id', 'selectedCustomerRow')
            customerSelectBtn.removeAttribute('disabled')
        })
    })
})

customerSelectBtn.addEventListener('click', () => {
    let selectedCustomerRow = document.getElementById('selectedCustomerRow')
    customerFormInputFields[0].value =
        selectedCustomerRow.children[0].textContent
    customerFormInputFields[1].value =
        selectedCustomerRow.children[1].textContent
    customerFormInputFields[2].value =
        selectedCustomerRow.children[2].textContent
    customerFormInputFields[3].value =
        selectedCustomerRow.children[3].textContent
    customerFormInputFields[4].value =
        selectedCustomerRow.children[4].textContent
    customerFormInputFields[5].value =
        selectedCustomerRow.children[5].textContent

    customerCloseImg.click()
    customerSelectBtn.setAttribute('disabled', true)
})

//displaying products container on entering the keyboard shortcut ctrl_q
let productId = document.getElementById('pId')
let productContainer = document.getElementById('product-container')
let productCloseImg = document.getElementById('product-close-img')

productId.addEventListener('keyup', e => {
    if (e.ctrlKey && e.key == 'q') {
        productContainer.classList.remove('d-none')
        document.getElementById('search-product').focus()
    }
})

//Stock wise product fetching start here
let productStockContainer = document.getElementById('viewByStock')
let productStock = document.getElementById('availableStock')

function removeOldStockRows () {
    let oldRows = document.querySelectorAll('#availableStock .tableRow')
    if (oldRows.length > 0) {
        oldRows.forEach(e => {
            e.remove()
        })
    }
}

function showStock (data) {
    let i = 1
    data[0].inventory.forEach(ele => {
        if (ele.current_quantity > 0) {
            let tr = document.createElement('tr')
            let td = document.createElement('td')
            let td1 = document.createElement('td')
            let td2 = document.createElement('td')
            let td3 = document.createElement('td')
            let td4 = document.createElement('td')
            let td5 = document.createElement('td')
            let td6 = document.createElement('td')

            td.textContent = i
            td1.textContent = ele.sale_rate
            td2.textContent = ele.MRP
            td3.textContent = ele.current_quantity
            if (ele.MFD != null)
                td4.textContent = ele.MFD.split('-').reverse().join('-')
            else td4.textContent = 'NA'
            if (ele.EXP != null)
                td5.textContent = ele.EXP.split('-').reverse().join('-')
            else td5.textContent = 'NA'
            td6.textContent = ele.inventory_id
            tr.classList.add('tableRow')
            td6.classList.add('d-none')
            tr.append(td)
            tr.append(td6)
            tr.append(td1)
            tr.append(td2)
            tr.append(td3)
            tr.append(td4)
            tr.append(td5)

            productStock.append(tr)
            i++
        }
    })

    let tr = document.createElement('tr')
    tr.classList.add('tableRow')
    let td = document.createElement('td')
    td.setAttribute('colspan', '6')
    td.innerHTML = '...'
    tr.append(td)
    productStock.append(tr)
}

function getProductStockFromDB (productId) {
    fetch(`bill/${productId.trim()}/get`)
        .then(res => res.json())
        .then(data => {
            if ('error' in data[0]) alert(data[0].error)
            else {
                productStockContainer.classList.remove('d-none')
                removeOldStockRows()
                showStock(data)
                inputField[0].value = data[0].id
                inputField[1].value = data[0].product_id
                inputField[2].value = data[0].product_name
                inputField[3].value = data[0].quantity
                inputField[4].value = data[0].rate
                inputField[5].value = data[0].MRP
                inputField[6].value = data[0].discount
            }
        })
}

inputField[1].addEventListener('keyup', event => {
    if (
        event.key == 'Enter' &&
        inputField[1].value != null &&
        inputField[1].value != ''
    ) {
        getProductStockFromDB(inputField[1].value)
    }
})

manageProductInfo()

let productSelectBtn = document.getElementById('product-select-button')
productSelectBtn.addEventListener('click', () => {
    let selectedProductRow = document.getElementById('selectedProductRow')
    getProductStockFromDB(selectedProductRow.children[1].textContent)
    productCloseImg.click()
    productSelectBtn.setAttribute('disabled', true)
})

//closing the product select sub window
let closeStock = document.querySelector('#close-sub-stock')
closeStock.addEventListener('click', e => {
    e.preventDefault()
    productStockContainer.classList.add('d-none')
})

//select product by stock
let productStockSelectBtn = document.querySelector('#product-sub-info-btn')
let stockObserve = new MutationObserver(() => {
    let stockRows = document.querySelectorAll('#availableStock .tableRow')
    stockRows.forEach(e => {
        e.addEventListener('click', event => {
            event.preventDefault()
            stockRows.forEach(hide => {
                e.style.backgroundColor = 'blue'
                e.style.color = 'white'
                e.setAttribute('id', 'selectedProductStockRow')
                hide.removeAttribute('style')
                hide.removeAttribute('id')
                if (e.hasAttribute('id')) {
                    productStockSelectBtn.removeAttribute('disabled')
                } else {
                    productStockSelectBtn.setAttribute('disabled', true)
                }
            })
        })
    })
})
qty.addEventListener('input', e => {
    let v = parseInt(sessionStorage.getItem('productQty'))
    let v1 = parseInt(qty.value)
    if (v != null) {
        if (v1 > v) {
            qty.value = v
        }
    }
})
productStockSelectBtn.addEventListener('click', e => {
    e.preventDefault()
    let selectedStockRow = document.getElementById('selectedProductStockRow')
    sessionStorage.setItem(
        'productQty',
        selectedStockRow.children[4].textContent
    )
    let inventId = selectedStockRow.children[1].textContent
    quantity.setAttribute(
        'maxlength',
        selectedStockRow.children[3].textContent.length
    )
    document.getElementById('max-qty').value =
        selectedStockRow.children[4].textContent
    inputField[4].value = selectedStockRow.children[2].textContent
    inputField[5].value = selectedStockRow.children[3].textContent
    inventoryId.value = inventId
    quantity.focus()
    productStockSelectBtn.setAttribute('disabled', true)
    addBtn.removeAttribute('disabled')
    closeStock.click()
})

stockObserve.observe(productStock, { childList: true })
//enabling side buttons
let customerSelectForm = document.getElementById('customerSelectForm')
let method = document.getElementById('method')
let btn = document.querySelectorAll('.buttons button')
window.addEventListener('load', e => {
    sessionStorage.removeItem('preventForSwitch')
})
btn[0].addEventListener('click', e => {
    e.preventDefault()
    window.location.reload()
})

btn[1].addEventListener('click', e => {
    e.preventDefault()
    customerFormInputFields[7].removeAttribute('readonly')
    customerFormInputFields[8].removeAttribute('readonly')
    customerFormInputFields[7].focus()
    btn[0].setAttribute('disabled', true)
    btn[1].setAttribute('disabled', true)
    btn[2].setAttribute('disabled', true)
    method.innerHTML = ''
    let input = document.createElement('input')
    input.setAttribute('name', '_method')
    input.setAttribute('type', 'hidden')
    input.setAttribute('value', 'PUT')
    method.append(input)
    sessionStorage.clear()
    sessionStorage.setItem('update', 'updateData')
})

btn[2].addEventListener('click', e => {
    e.preventDefault()
    customerFormInputFields[7].removeAttribute('readonly')
    customerFormInputFields[8].removeAttribute('readonly')
    customerFormInputFields[7].focus()
    btn[0].setAttribute('disabled', true)
    btn[1].setAttribute('disabled', true)
    btn[2].setAttribute('disabled', true)
    method.innerHTML = ''
    let input = document.createElement('input')
    input.setAttribute('name', '_method')
    input.setAttribute('type', 'hidden')
    input.setAttribute('value', 'DELETE')
    method.append(input)
    sessionStorage.clear()
    sessionStorage.setItem('delete', 'deleteData')
})

document.getElementById('reset').addEventListener('click', e => {
    e.preventDefault()
    if (
        !customerFormInputFields[7].hasAttribute('readonly') &&
        !customerFormInputFields[8].hasAttribute('readonly')
    ) {
        customerFormInputFields[7].setAttribute('readonly', true)
        customerFormInputFields[8].setAttribute('readonly', true)
    }

    btn[0].removeAttribute('disabled')
    btn[1].removeAttribute('disabled')
    btn[2].removeAttribute('disabled')
})

//get the data from database for editing

function removeOldBillRows () {
    let billTableRows = document.querySelectorAll('#bill-table .bill-row')
    if (billTableRows.length > 0) {
        billTableRows.forEach(row => {
            row.remove()
        })
    }
}

customerFormInputFields[7].addEventListener('keyup', event => {
    event.preventDefault()
    if (event.key == 'Enter') {
        let date = customerFormInputFields[8].value.trim()
        let billNumber = customerFormInputFields[7].value.trim()

        fetch(`/bill/${billNumber}/edit/${date}`)
            .then(res => res.json())
            .then(data => {
                if ('error' in data[0]) {
                    alert(data[0].error)
                } else {
                    removeOldBillRows()
                    if (data[0].bill_customer != null) {
                        customerFormInputFields[0].value =
                            data[0].bill_customer.id
                        customerFormInputFields[1].value =
                            data[0].bill_customer.customer_name
                        customerFormInputFields[2].value =
                            data[0].bill_customer.customer_email
                        customerFormInputFields[3].value =
                            data[0].bill_customer.contact
                        customerFormInputFields[4].value =
                            data[0].bill_customer.customer_address
                        customerFormInputFields[5].value =
                            data[0].bill_customer.pending_amt
                    } else {
                        for (let i = 0; i <= 5; i++) {
                            customerFormInputFields[i].value = ''
                        }
                    }

                    data[0].bill_product.forEach(e => {
                        inputField[0].value = e.id
                        inputField[1].value = e.product_id
                        inputField[2].value = e.product_name
                        inputField[3].value = e.pivot.newQuantity
                        inputField[4].value = e.pivot.newRate
                        inputField[5].value = e.pivot.newMRP
                        inputField[6].value = e.pivot.newDiscount
                        let arr = new Array(2)
                        for (
                            let i = 0;
                            i < data[0].bill_inventory.length;
                            i++
                        ) {
                            if (data[0].bill_inventory[i].product_id == e.id) {
                                arr[0] = data[0].bill_inventory[i].inventory_id
                                arr[1] =
                                    data[0].bill_inventory[i].current_quantity
                                break
                            }
                        }
                        inputField[7].value = arr[0]
                        inputField[8].value = arr[1]
                        sessionStorage.setItem('productQty', arr[1])
                        addBtn.removeAttribute('disabled')
                        addBtn.click()
                        addBtn.setAttribute('disabled', true)
                    })
                    sessionStorage.setItem('preventForSwitch', 'true')
                    customerSelectForm.setAttribute(
                        'action',
                        `http://localhost:8000/bill/${billNumber}/${date}`
                    )
                }
            })
    }
})

document.addEventListener('visibilitychange', e => {
    let billNo = document.getElementById('billNumber').value
    if (sessionStorage.getItem('preventForSwitch') == null) {
        localStorage.removeItem('bill_no')
        localStorage.setItem('bill_no', billNo)
    }
})

window.addEventListener('storage', e => {
    let b = localStorage.getItem('bill_no')
    let billNo = document.getElementById('billNumber')
    if (b != null && sessionStorage.getItem('preventForSwitch') == null) {
        billNo.value = parseInt(b)
    }
    localStorage.removeItem('bill_no')
})
document.addEventListener('contextmenu', e => {
    e.preventDefault()
})
