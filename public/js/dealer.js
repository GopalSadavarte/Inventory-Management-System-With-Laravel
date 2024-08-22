export function manageDealerInfo () {
    let dealerName = document.getElementById('dealerName')
    let dealerCloseImg = document.getElementById('dealer-close-img')
    let dealerContainer = document.getElementById('dealer-container')
    let searchDealer = document.getElementById('search-dealer')

    dealerName.addEventListener('keyup', e => {
        e.preventDefault()
        if (e.key == 'q' && e.ctrlKey) {
            dealerContainer.classList.remove('d-none')
            searchDealer.focus()
        }
    })

    dealerCloseImg.addEventListener('click', e => {
        e.preventDefault()
        dealerContainer.classList.add('d-none')
    })

    let dealerSelectBtn = document.getElementById('dealer-select-button')
    let dealerTableRows = document.querySelectorAll('#dealerTable .tableRow')
    dealerTableRows.forEach(ele => {
        ele.addEventListener('click', e => {
            e.preventDefault()
            dealerTableRows.forEach(hideRow => {
                ele.classList.add('bg-primary', 'text-light')
                hideRow.classList.remove('bg-primary', 'text-light')
                ele.setAttribute('id', 'selectedDealerRow')
                dealerSelectBtn.removeAttribute('disabled')
                if (hideRow.hasAttribute('id') && dealerTableRows.length > 1)
                    hideRow.removeAttribute('id')
            })
        })
    })

    searchDealer.addEventListener('keyup', e => {
        let searchValue = searchDealer.value.toUpperCase()
        let searchKeys = document.querySelectorAll('#dealerTable tr .searchKey')
        searchKeys.forEach(ele => {
            let eleValue = ele.textContent.toUpperCase()
            if (eleValue.indexOf(searchValue) > -1) {
                ele.parentElement.style.display = ''
                ele.parentElement.click()
                if (e.key == 'Enter') {
                    dealerSelectBtn.click()
                }
            } else {
                ele.parentElement.style.display = 'none'
            }
        })
    })

    let dealerEle = document.querySelectorAll('.dealer')
    dealerSelectBtn.addEventListener('click', e => {
        e.preventDefault()
        let row = document.getElementById('selectedDealerRow')
        dealerEle[0].value = row.children[0].textContent
        dealerEle[1].value = row.children[1].textContent
        dealerEle[2].value = row.children[2].textContent
        dealerEle[3].value = row.children[3].textContent
        dealerEle[4].value = row.children[4].textContent

        dealerCloseImg.click()
    })
}
