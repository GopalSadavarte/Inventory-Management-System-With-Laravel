export function manageProductInfo (purchaseOrStockEntry = null) {
    let productContainer = document.getElementById('product-container')
    let productCloseImg = document.getElementById('product-close-img')

    productCloseImg.addEventListener('click', () => {
        productContainer.classList.add('d-none')
        if (purchaseOrStockEntry != null) {
            let created = document.getElementById('clickedRow')
            created.removeAttribute('id')
        }
    })

    let tableRows = document.querySelectorAll('#productTable .tableRow')
    let productSelectBtn = document.getElementById('product-select-button')
    tableRows.forEach(tr => {
        tr.addEventListener('click', () => {
            tableRows.forEach(hideRow => {
                hideRow.classList.remove('bg-primary', 'text-light')
                tr.classList.add('bg-primary', 'text-light')
                hideRow.removeAttribute('id')
                tr.setAttribute('id', 'selectedProductRow')
                productSelectBtn.removeAttribute('disabled')
            })
        })
    })

    let searchBar = document.getElementById('search-product')
    let pName = document.querySelectorAll('.searchKey')
    searchBar.addEventListener('keyup', e => {
        e.preventDefault()
        let searchedValue = searchBar.value.toUpperCase()
        pName.forEach(name => {
            let v = name.textContent.toUpperCase()
            if (v.indexOf(searchedValue) > -1) {
                name.parentElement.style.display = ''
                name.parentElement.click()
                if (e.key == 'Enter') {
                    productSelectBtn.click()
                }
            } else {
                name.parentElement.style.display = 'none'
            }
        })
    })
}
