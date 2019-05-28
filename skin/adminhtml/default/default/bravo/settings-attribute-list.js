/*
     *      CARICA LISTA ATTRIBUTI IN BASE A SET E GRUPPO
     */
    function oddizeRows() {
        //
        document.querySelectorAll('#attrs-data li').forEach(function(item, i) {
            item.classList.remove("even");
            if (i%2==0) {
                item.classList.add("even");
            }
        });
    }
    function loadAttributeList() {
        
        var localattributes = [];

        var endpoint = '/api/getAttributeByGroup.php';
        var setId    = document.getElementById('sets-select').value;
        var groupId  = document.getElementById('group-select').value;

        var params = '?setid=' + setId + '&groupid=' + groupId;

        if (setId==null || setId=='' || groupId==null || groupId=='') {
            alert('impossibile caricare la lista: errore nel leggere i valori di set e group');
            return;
        }

        endpoint += params;

        fetch(endpoint)
            .then(blob => blob.json())
            .then(data => {
                document.getElementById('attribute-list').innerHTML = data.html;
                var el = document.getElementById('attrs-data');
                var sortable = Sortable.create(el, {
                    onEnd: function () {
                        oddizeRows();
                    },
                });
                
                document.querySelectorAll('.remove-attr').forEach(function(item) {
                    item.addEventListener('click', function() {
                        document.querySelector('#remove-attribute-form [name=setid]').value = document.getElementById('sets-select').value;
                        document.querySelector('#remove-attribute-form [name=groupid]').value = document.getElementById('group-select').value;
                        document.querySelector('[name=remove_attribute_entity_id]').value=this.getAttribute('data-attentid');
                        document.querySelector('#remove-attribute-form').submit();
                    });
                });
            });
        
    }
    
window.onload = function() {

    /*
     *  Seleziona solo
     */
    var e = document.getElementById('sets-select');

    e.addEventListener("change", function() {

        var value = this.value;

        document.querySelectorAll('.group-opt').forEach(function(item) {
            item.style.display = 'block';
            if (item.getAttribute('data-setid')!=value) {
                item.style.display = 'none';
            }
        });

        document.getElementById('group-select').removeAttribute('disabled');

    });
    
    if (document.getElementById('sets-select').value!='') {
        document.getElementById('group-select').removeAttribute('disabled');
    }

    
    
    
    document.getElementById('save-list-button').addEventListener('click', function(e) {
        document.querySelectorAll('.posholder').forEach(function (item, i) {
            var j=i+1;
            item.value = j;
        });
        document.querySelector('#save-list-form [name=setid]').value = document.getElementById('sets-select').value;
        document.querySelector('#save-list-form [name=groupid]').value = document.getElementById('group-select').value;
    });
    


    /*
     *      CHECK DEI CAMPI
     */
    document.getElementById('submit-button').addEventListener('click', function(e) {

        if (document.getElementById('attribute-id').value=='') {
            alert('Attributo non valido');
            e.preventDefault();
            return false;
        }
        if (document.getElementById('sets-select').value=='') {
            alert('Set non valido');
            e.preventDefault();
            return false;
        }
        if (document.getElementById('group-select').value=='') {
            alert('Gruppo non valido');
            e.preventDefault();
            return false;
        }
        
        document.querySelector('#add-attribute-form [name=set_id]').value = document.getElementById('sets-select').value;
        document.querySelector('#add-attribute-form [name=group_id]').value = document.getElementById('group-select').value;

    });

    

    const groupInput = document.querySelector('#group-select');

    groupInput.addEventListener('change', loadAttributeList);


    /*
     *      AUTO COMPLETE ATTRIBUTI
     */
    const endpoint = '/api/getAttributesAPI.php';

    const attributes = [];

    // fetch grabs endpoint - at this point a promise and generates readablestream
    fetch(endpoint)
      .then(blob => blob.json())
      .then(data => attributes.push(...data));

    function findMatches(keyword, attributes) {
      return attributes.filter(attribute => {
        // does city or state match? use paramater regex
        const regex = new RegExp(keyword, 'gi');
        if (null != attribute.label) {
            return attribute.label.match(regex)
        }
      });
    }

    function displayMatches() {
        const matchArray = findMatches(this.value, attributes);
        const suggestions = document.querySelector('#suggestion');

        const html = matchArray.map(attribute => {
            var id = attribute.id;
            var label = attribute.label;
            var value = attribute.value;
            return '<div class="attribute-option" value="' + id + '">'+ label +'</div>';
        }).join('');

        if (html.length>0) {
            suggestions.style.display = 'block';
        } else {
            suggestions.style.display = 'none';
        }

        suggestions.innerHTML = html;

        document.querySelectorAll('.attribute-option').forEach(function(item) {
            item.addEventListener('click', function() {
                var val = this.getAttribute('value');
                var label = this.innerHTML;
                document.querySelector('#attribute-id').value = val;
                document.querySelector('#attribute-input').value = label;

                setTimeout(function() {
                    document.querySelector('#suggestion').style.display = 'none';
                }, 200);
            });
        });
    }

    const searchInput = document.querySelector('#attribute-input');
    const suggestions = document.querySelector('#suggestion');

    searchInput.addEventListener('change', displayMatches);
    searchInput.addEventListener('keyup', displayMatches);

    window.addEventListener('click', function(e) {
        if (e.target.id!='') {
            document.querySelector('#suggestion').style.display = 'none';
        }
    });

};

