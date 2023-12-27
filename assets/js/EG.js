/**get page data ie ALL page data,
    option 1    
        brack it doun to small chunks and as the return load them 
        PROS:   A.
        CONS:   A.
    option 2 
        make One call and One responce 
        PROS:   A. Everything is there as we start loading
        CONS:   A. if one pare fails we fail it all


Dependencys:
    jquery.js
    toastr.min.js
    moment.min.js
    daterangepicker.js
    tui-grid.min.js

api structure:
    any api call (GET & POST [and put patch del]) must staer with 'ep' (myapp.com?ep=...)
    other data/endpoints can be attached
    for dates we use ds [date start] and de [date end]

    // ep:getPageTables&page=page

HTML
    to start we need only Tow elements 
    1. div with id grid (hook for TUI)
        <div id="grid"></div>
    2. wrapper with id myTab (hook fo navigation tabs)
        <ul id="myTab"></ul>
*/

(function(global, $){

    // event handler using Jquery
    const EventHandler = {

        /**
         * @method on
         * @description performs a jquery on event
         * @param {String} event - The event to listen to.
         * @param {String} selectors - The new value to be set.
         * @param {function} handler - The callback - to be run when the event is triggerd.
        */
        on(selector, event, handler) {
          $(document).on(event,selector,handler);
        },

         /**
         * @method each
         * @description proforms a jquery on event
         * @param {String} selectors - The new value to be set.
         * @param {function} handler - The callback - to be run when on each element.
        */
        each(selector, handler) {
            $(selector).each(handler)
        },
    }
    class SideBar {
        // TODO add a toggle to lock a postion
        constructor(){
            // getstate form local storage
            this.state = {
                auto:false,
                open:false,
            }
            const storedState = localStorage.getItem('sidebarState');
            if (storedState) {
                try {
                    this.state = JSON.parse(storedState);
                } catch (error) {
                    console.error('Error parsing stored sidebar state:', error);
                }
            }
            this.updateSidebarState();
        }

        updateSidebarState() {
            $('html').addClass('sidebar-close');
            if (this.state.auto && this.state.open) {
                // If auto is true and the sidebar is open, remove the sidebar-close class
                $('html').removeClass('sidebar-close');
            }
        }

        toggleSidebar() {
            this.state.auto = !this.state.auto;
            this.state.open = !this.state.open;
            // this.state.auto = !this.state.auto;

            // Save the updated sidebar state to localStorage
            localStorage.setItem('sidebarState', JSON.stringify(this.state));

            // Add smooth animation when opening and closing the sidebar
            this.updateSidebarState();

        }


        handleSidebarHover(shouldOpen) {
            if (this.state.auto) {
                // Only handle hover if auto is true
                $('html').toggleClass('sidebar-close', !shouldOpen);
            }
        }

        make_element_active(target, event){
            // event.preventDefault();
            $('#sidebar a').removeClass('fs-nav-active');
            $(target).addClass('fs-nav-active');
        }
     
    
    }

    const x = new SideBar()

    var opend_sidebar_menu
    EventHandler.on('#sidebar a', 'click', (e) => x.make_element_active(this, e));
    EventHandler.on('#sidebar .toggle', 'click', () => x.toggleSidebar());
    EventHandler.on('#sidebar', 'mouseenter', () => x.handleSidebarHover(true));
    EventHandler.on('#sidebar', 'mouseleave', () => x.handleSidebarHover(false));

    EventHandler.each('.sidebar-icon-link .arrow',function(index){
        $(this).on('click',function(){
            if(index !== opend_sidebar_menu){
                $('.nav-links li').removeClass("showMenu")
            }
            $(this).parent().parent().toggleClass("showMenu")
            opend_sidebar_menu = index
        })
    })

    class EGTable {

        constructor(props){

            /**
             * main veriables
            */
            this.page = props.page || '';
            this.api = props.api || '';
            this.tabIndex = 0;

            /**
             * store 
            */
            this.store = new Map(); //state managment
            this.callbackQueue = []; //state update Queue
            
            /**
             * tui grid setup
            */
            this.tuiConfig = {
                el:document.getElementById('grid'),
                bodyHeight: 'fitToParent',
                columns: [],
                data: [],
                ...props.tuiConfig
            }
            // do this AFTER the config data is recived
            this.GRID = new tui.Grid(this.tuiConfig)
            this.applyGridTheme(props.gridTheme)

            /**
             * other veriables
            */ 
            this.dateRange = props.dateRange || "ds=2023-01-01&de=2023-12-31";
            
            /**
             * initiolise app
             */
            this.start()
            this.bindEvents()
        }


        




        //store methods

        /**
         * @method generateKey
         * @description Converts any input to a uniqe string key.
         * @param {Object|Array|String} key - The cell object with {columnName, rowKey, page, tableName}.
         * @throws {Error}  - Throws error if givi a boolean or an undefined value
         * @returns {string} - The string key representing the cell object.
        */ 
        generateKey(key) {
            if (typeof key === 'boolean' || key === null || key === undefined) {
                throw new Error('Invalid input: Boolean, null, or undefined values are not allowed.');
            }
            const inputString = JSON.stringify(key);
            let hash = 0;
            for (let i = 0; i < inputString.length; i++) {
                // Get the character code of the current character
                const char = inputString.charCodeAt(i);
                // Update the hash using a simple hashing algorithm
                hash = (hash << 5) - hash + char;

                // Convert the hash to a 32-bit integer
                hash = hash & hash;
            }
            // Ensure the hash is non-negative and convert it to a base-36 string
            hash = (hash >>> 0).toString(36);
            return hash;
        }


        /**
         * @method setState
         * @description Sets the initial state for a given cell.
         * @param {Object|Array|String} key - The cell object with {columnName, rowKey, page, tableName}.
         * @param {string|number|Array|Object} startValue - The initial value for the cell.
        */
        setState(key, value) {
            // if (key === null || key === undefined) {
            //     throw new Error('Invalid value: null or undefined values are not allowed.');
            // }
            const isObjectOrArray = typeof value === 'object' && value !== null;
            const storedValue = isObjectOrArray
            ? Array.isArray(value)
                ? [...value] // If it's an array, store a new array with the same values
                : { ...value } // If it's an object, store a new object with the same properties
            : value;

            this.store.set(this.generateKey(key) , {
                startValue: isObjectOrArray ? Array.isArray(storedValue) ? [...storedValue] : { ...storedValue } : storedValue,
                // storedValue, // first nital value of when calling set state (never changes)
                prevValue: null,
                value:storedValue, // 
                lastValidValue: null,
                isValid: false,
                errorMessage: '' 
            });
        }

        /**
         * @method logState
         * @description Logs the state Object used for testing.
        */
        logState(msg) {
            const statesCopy = new Map([...this.store]);
            // Create a new Error object to capture the stack trace
            const error = new Error();
            // Extract the stack trace information
            const stackTrace = error.stack.split('\n')[2].trim();
            console.log(`${msg ||'Log State: no msg provided'}\n${stackTrace}`)
            console.log(statesCopy)
        }

        /**
         * @method getStateValidity
         * @description Checks if the current state for a given cell is valid.
         * @param {Object|Array|String} key - The cell object with {columnName, rowKey, page, tableName}.
         * @returns {Object|null} - Object with isValid, errorMessage, and lastValidValue properties, or null if the state does not exist.
        */
        getStateValidity(key) {
            const state = this.store.get(this.generateKey(key));
            return state ? {
                    isValid: state.isValid,
                    errorMessage: state.errorMessage,
                    lastValidValue: state.lastValidValue,
                    value:state.value
                }: null;
        }


        /**
         * @method updateState
         * @description Updates the state with validation and queues callbacks (used for asyncrones state updates).
         * @param {Object|Array|String} key - The cell object with {columnName, rowKey, page, tableName}.
         * @param {Object|Array|String} nextValue - The new value to be set.
         * @param {boolean} useValidateCallback - Flag to determine if to use a callback to validate the sate for asyncrones updates.
         * @returns {Object|Function|null} - Callback to validate the new state or null if the state does not exist.
        */
        updateState(key, nextValue, useValidateCallback = true) {

            const state = this.store.get(this.generateKey(key));

            if (state) {
                if(!useValidateCallback){
                    state.prevValue= state.value //Optionaly store the whols state here.
                    state.value = nextValue
                    this.store.set(this.generateKey(key), { ...state});
                    return {...state} // a copy of the state
                }
                // TODO: a updateState later callback without validity
                const validateState = (isValid, message) => {
                    const callback = () => {
                        state.isValid = isValid;
                        state.errorMessage = isValid ? '' : message || 'Validation failed.';
                        state.prevValue = state.value;
                        state.value = nextValue;
                        if (isValid) {
                            state.lastValidValue = nextValue;
                        } 
                        this.store.set(this.generateKey(key), { ...state});
                    };

                    this.callbackQueue.push(callback);
                    this.processQueue();


                    return { ...state };
                };

                return validateState;
            }

            return null;
        }

        /**
         * @method processQueue
         * @description Processes the callback queue in order.
        */
        processQueue() {
            while (this.callbackQueue.length > 0) {
                const callback = this.callbackQueue.shift();
                try {
                    callback();
                } catch (error) {
                    // Handle the error gracefully (log it, etc.)
                    console.error('Error in callback:', error);
                }
            }
        }

        /**
         * @method destroyState
         * @description Destroys the state for a given cell.
         * @param {Object|Array|String} key  - The cell object with {columnName, rowKey, page, tableName}.
        */
        destroyState(key) {    
            this.store.delete(this.generateKey(key));
        }

        /**
         * @method readState
         * @description Reads the state with access to first value, last valid value, and current value.
         * @param {Object|Array|String} key - The cell object with {columnName, rowKey, page, tableName}.
         * @returns {Object|null} - Object with startValue, prevValue, value, lastValidValue, isValid, and errorMessage properties, or null if the state does not exist.
        */
        readState(key) {
            const state = this.store.get(this.generateKey(key));
            return state ? {...state} : null;
        }


        //Helpers

        /**
         * @method urlBuilder
         * @description constructs a url based on paramentors passed.
         * @param {String} ep - The EndPoint in the API .
         * @param {Array} other - Array of other getters to set.
         * @returns {Srting} - fully bults url string.
         */
        urlBuilder(ep='',other=[]){
            var params='';
            if(other.length > 0){
                other.forEach(a=>{params = params+'&'+a});
            }
            return this.api+'?ep='+ep+params;
        }

        /**
         * @method loadStateFromLocalStorage
         */    
        loadStateFromLocalStorage() {
            const savedState = localStorage.getItem(`${this.page}AppState`);

            if (savedState) {
                const parsedState = JSON.parse(savedState);
                // Restore state variables
                this.tabIndex = parsedState.tabIndex || 0;
                this.dateRange = parsedState.dateRange ||this.dateRange ;
                // Add more state variables as needed
            }
        }
         /**
         * @method saveStateToLocalStorage
         */  
        saveStateToLocalStorage() {
            const stateToSave = {
                tabIndex: this.tabIndex,
                dateRange: this.dateRange,
                // Add more state variables as needed
            };
            localStorage.setItem(`${this.page}AppState`, JSON.stringify(stateToSave));
        }


        /**
        * @method setActiveTab
        */ 

        setActiveTab(activeEl = false){
            $('#myTab button').removeClass('active')
            if(activeEl){
                activeEl.addClass('active')
            } else{
                $(`#myTab button[data-index='${this.tabIndex}']`).addClass('active')
            }
        }


        /**
         * @method ajax
         * @description proforms a $ get request.
         * @param {String} url - Teh URL endpoint.
         * @param {String} methos - The method to proform the request. Default = GET
         * @param {Object} data - Post data's Objet|| formData.
         * @throws {Error} Throws an error if fail or if status is not success.
         * @returns {any} - return value.
         */
        async ajax(url,methos="GET",data = {}){
            console.log(data);
            try {
                const res = await $.ajax({
                    type: methos, 
                    url,
                    data,
                    
                })
                console.log(res);
                if (res.status !== 'success') {
                    throw new Error(res.message);
                }
        
                return res.data;

            } catch (err) {
                throw new Error(err.statusText || err.responseText || err);
            }
        }



        /**
         * @method addNav
         * @description addes navgation btn to #myTab element.
         * @TODO make the tabes avalable to be loaded by user (classes elements ect...)
         */
        addNav(){
            $('#myTab').empty()
            const tables = this.readState('tables').value
            tables.forEach((e,i)=>{
                var container = $('<li/>').addClass("nav-item"),
                button = $('<button/>')
                .attr({
                    "id": e.name+"_tab",
                    "data-index": i,
                    "type": "button",
                    "role":"tab"
                })
                .addClass(`nav-link ${i===0 ?'active':''}`).text(e.display_name);
                container.append(button)
                $('#myTab').append(container)                
                // console.log(button);
            })
        }
        
        //setUp
        applyGridTheme(theme){
            window.tui.Grid.applyTheme('default',theme? theme: {
                outline: {
                    border: 'rgb(217, 224, 232)',
                },
                area: {
                    header: {
                        border: 'rgb(217, 224, 232)',
                    },
                },
                cell: {
                    header: {
                        border: 'rgb(217, 224, 232)',
                        showVerticalBorder: false,
                    },
                },
            })
        }

        async start(){
            try{
                var url = this.urlBuilder('pageTables',['page='+this.page]);//getPageTables || pageTables
                const tables = await this.ajax(url);
                this.setState('tables',[...tables])
                // this.logState()//debug
                const storedTales = this.readState('tables')
                const updatedTables = await this.checkFormattors(storedTales)
                this.updateState('tables',updatedTables,false)
                this.addNav()
                this.loadStateFromLocalStorage()
                this.setTable()
            }catch(err){
                console.log(err)
            }
            

        }


        setTable(){
            const columns = this.readState('tables')
            this.GRID.setColumns(columns.value[this.tabIndex].columns)
            this.setTableData()
            this.setModal()
            this.setActiveTab()
        }

        setModal(){
            var _this =this
            try{
                var index = _this.tabIndex
                const tables = _this.readState('tables').value
                const url = "includes/modal_api.php?modal="+tables[index].modal
                const oldModaeInputs = _this.readState("modaeInputs") 
                if(oldModaeInputs){
                    while(oldModaeInputs.value.length >0){
                        _this.destroyState(oldModaeInputs.value.shift()+"FormInput",value)
                    }
                    _this.destroyState("modaeInputs") 
                }
               
                var modaeInputs = []
                $.get(url).then(res=>{
                    $('#modal_go_here').empty().append(res).find('input').each(function() {
                        _this.setState($(this).attr('name')+"FormInput",'')
                       
                        modaeInputs.push($(this).attr('name'))    
                    })
                    _this.setState("modaeInputs",modaeInputs)
                }).catch(err=>{
                    throw new Error(err)
                })
            }catch(err){
                console.log(err);
            }
            
        }

        async setTableData(){
            var index = this.tabIndex
            const tables = this.readState('tables').value
            const url = this.urlBuilder('get_table',['table='+tables[index].name,this.dateRange])
            try {
                const responce = await this.ajax(url)
                this.GRID.resetData(responce)
            } catch (error) {
                console.log(error);
                this.GRID.resetData([])
            }
        }

        async checkFormattors(tables){
            for (const tbl of tables.value) {
                for (const col of tbl.columns) {
                    if(col.hasOwnProperty('formatter')){
                        // col.formatter = {fx:'name'} ---exsample
                        var table = Object.keys(col.formatter)[0]
                        var column = Object.values(col.formatter)[0] 
                        var url = this.urlBuilder('getIdVal',["table="+table,"column="+column]);
                        try{
                            const formmaterArr = await this.ajax(url);
                            this.setState(tbl.name+'Formatter',[...formmaterArr])
                            // this.logState()//debug
                            col.formatter = (val)=>{
                                const formatArr = this.readState(tbl.name+'Formatter')
                                var format = formatArr.value.find(el=>el.id === parseInt(val.value))
                                console.log();
                                return format ? format[column] : val.value;
                            }
                        }catch(err){
                            console.log(err)
                        }
                    }
                }
            }
            return tables.value
        }
        /**
         * @method on
         * @description performs a jquery on event
         * @param {String} event - The event to listen to.
         * @param {String} selectors - The new value to be set.
         * @param {function} handler - The callback - to be run when the event is triggerd.
        */
        on(event,selector, handler) {
            $(document).on(event,selector,handler);
        }
    
        /**
         * @method each
         * @description proforms a jquery on event
         * @param {String} selectors - The new value to be set.
         * @param {function} handler - The callback - to be run when on each element.
         */
        each(selector, handler) {
            $(selector).each(handler)
        }
/**
         * @method validateField
         * @description proforms a jquery on event
         * @param {Object} valueObj - The key value to validate -> {fieldName,value}.
         * @param {Object|null} key - The store key to update the state.
         */


        async validateField(valueObj,key=null) {
            var _this = this

            //key = {columnName,rowKey} || fieldName+"FormInput"
            if(!key || key === null){
                key = valueObj.fieldName+"FormInput"
            }
            
            var validatorCB =_this.updateState(key,valueObj.value)

            var response = await _this.validateFealdData(valueObj)

            var validityState = await validatorCB(response.data.isValid,response.data.message)
            console.log(validityState);
            return validityState

            // //do the logic  
            // if(validityState.isValid){
            //     field.removeClass('is-invalid').addClass('is-valid').siblings(".error-message").text(validityState.errorMessage)
            // }else{
            //     // Display the error message returned from the server
            //     field.removeClass('is-valid').addClass('is-invalid').siblings(".error-message").text(validityState.errorMessage);
            // }            
        }

        async validateForm() {
            var _this = this
            var isValid = true;
            
            // Validate each form field
            $("#form_new_record input").each(function() {
                var el = $(this)
                var validity =  _this.validateField({fieldName:el.attr("name"),value:el.val().trim()});

                if ($(this).siblings(".error-message").text() !== "") {
                    isValid = false;
                }
            });
            
            return isValid;
        }
        
        sendDataToServer() {
            var _this = this
            // Collect form data
            var formData = $("#form_new_record").serializeArray();
            var dataObject = {};
        
            // Convert formData to a plain JavaScript object
            $.each(formData, function(index, field) {
                dataObject[field.name] = field.value;
            });
        
            // Send an AJAX request to the server
            const tables = _this.readState('tables').value
            const url = _this.urlBuilder('new_row',["table="+tables[_this.tabIndex].name])

            $.ajax({
                type: "POST",
                url: url,
                data: dataObject,  // Use the plain JavaScript object
                success: function(response) {
                    // Handle the server response
                    console.log(response);
                    if(response.status === "success"){
                        _this.GRID.appendRow(response.data)
                        toastr.info(response.status, 'Added successfuly!')
                        $('#modal_go_here').find('.btn-close').click()
                        $('#modal_go_here').find('input').each(function(input){
                            console.log($(this))
                            $(this).val('').removeClass(['is-valid','is-invalid']).siblings('.error-message').text('')
                        })
                    }
                },
                error: function(error) {
                    // Handle errors
                    console.error(error);
                }
            });
        }


          /**
         * @method validateFealdData
         * @description proforms 
         * @param {Object} data - {fealdName, value}
         * @throws {Error} Throws an error if fail or if status is not success.
         * @returns {any} - return value.
         */

        async validateFealdData(data){
            try {
                var _this = this
                const tables = this.readState('tables').value
                const url = this.urlBuilder('validate_field',['table='+tables[_this.tabIndex].name])
                const res = await _this.ajax(url,"POST",data)
                return res
            } catch (error) {
                console.log(error);
                return false
            }
            
        }

        bindEvents(){
            var _this = this

            // set tabs to active
            _this.on('click','#myTab button',function(){
                _this.tabIndex = $(this).attr('data-index')
                _this.setActiveTab($(this))
                _this.setTable()
            })

            // grid clear filters
            _this.on('click','#clear_Filetrs',()=>{
                const tables = _this.readState('tables').value
                tables[_this.tabIndex].columns.forEach(e=>{
                    _this.GRID.unfilter(e.name)
                })
            })





            // grid Events


            _this.GRID.on('editingStart',function(ev) {
                var {columnName,rowKey,value} = ev
                _this.setState({columnName,rowKey},value)
            })

            _this.GRID.on('beforeChange',function(ev) {
                var {columnName,rowKey} = ev.changes[0]

                var valid = _this.getStateValidity({columnName,rowKey})
                if(valid && !valid.isValid){
                    ev.stop()
                    _this.destroyState({columnName,rowKey})
                    toastr.error(valid.errorMessage, 'Validation Error!')
                }
            })

            _this.GRID.on('afterChange',async function(ev){
                var {columnName,rowKey} = ev.changes[0]
                var row = _this.GRID.getRow(rowKey)

                const tables = _this.readState('tables').value
                var valid = _this.getStateValidity({columnName,rowKey})
                if(valid && valid.isValid){
                    try{
                        const url = _this.urlBuilder('update_table',["table="+tables[_this.tabIndex].name])
                        const res = await _this.ajax(url,"POST",{fieldName: columnName, value: valid.value,id:row.id })
                        if(res){
                            window.toastr.info('Success')
                            _this.destroyState({columnName,rowKey})
                        }
                    }catch(err){
                        console.log(err);
                    }
                    _this.destroyState({columnName,rowKey})
                }
                })
             




            _this.on("input","#grid input.tui-grid-content-text",function(ev) {
                var  currentCell = _this.GRID.getFocusedCell()
                var {columnName,rowKey} = currentCell
                var input = $(this)
                var validatorCB =_this.updateState({columnName,rowKey},ev.target.value)

                const tables = _this.readState('tables').value
                const url = _this.urlBuilder('validate_field',["table="+tables[_this.tabIndex].name])

                $.ajax({
                    type: "POST",
                    url: url,
                    data: { fieldName: columnName, value: ev.target.value },
                    success: function(response) {
                        var validityState = validatorCB(response.data.isValid,response.data.message)
                        if(validityState.isValid){
                            input.css({ borderColor: '#71dd37' }).removeAttr('data-mdb-tooltip-init')
                            .removeAttr('data-mdb-ripple-init').removeAttr("data-mdb-placement").removeAttr("title")
                        }else{
                            input.css({ borderColor: '#ff3e1d' }).attr({
                                'data-mdb-tooltip-init':'',
                                'data-mdb-ripple-init':'',
                                "data-mdb-placement":"top",
                                "title":validityState.errorMessage
                            })
                        }
                    },
                    error: function(error) {
                        // Handle errors
                        console.error(error);
                    }
                });
            });


            _this.on('click', '.tui-select-box-item',function(ev){

                var  currentCell = _this.GRID.getFocusedCell()
                var {columnName,rowKey } = currentCell

                _this.validateField(field)

                var validatorCB =_this.updateState({columnName,rowKey },ev.target.dataset.value)

                const tables = _this.readState('tables').value
                const url = _this.urlBuilder('validate_field',["table="+tables[_this.tabIndex].name])

                $.ajax({
                    type: "POST",
                    url: url,
                    data: { fieldName: columnName, value: ev.target.dataset.value},
                    success: function(response) {
                        validatorCB(response.data.isValid,response.data.message)
                    },
                    error: function(error) {
                        // Handle errors
                        console.error(error);
                    }
                });

            })

            //Form Events
            _this.on("click","#submit_btn_new_record",function(){
                // Validate all fields before submitting
                if (_this.validateForm()) {
                    // If the form is valid, send the data to the server
                    _this.sendDataToServer();
                }
            })


            _this.on("input change","#form_new_record input, #form_new_record select",function() {
                _this.validateField($(this));
            });


            _this.on('hide.bs.modal','#basicModal',function(){
                $(this).find('input').each(function(input){
                    $(this).val('').removeClass(['is-valid','is-invalid']).siblings('.error-message').text('')
                })
            })




            //Loading events 
            window.addEventListener('beforeunload', () => this.saveStateToLocalStorage());
            window.addEventListener('load',() => this.loadStateFromLocalStorage());



        }
    }

    global.toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "1000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    //setup all components to be exposed to the main window/global
    var out = {
        SideBar,
        EGTable,
        
    }
    // expose EG to window/global
    global.EG = out



})(window, $)






