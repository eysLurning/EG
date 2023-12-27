(function(global, $){

    // event handler using Jquery
    const EventHandler = {
        on(selector, event, handler) {
          $(document).on(event,selector,handler);
        },
        each(selector, handler) {
            $(selector).each(handler)
          },
    }
    // const FORMATCURRENCY =(val)=>{
    //     switch (val) {
    //         case 2:
    //             return "USD"
    //             case 3:
    //             return "EUR"
    //             case 4:
    //             return "ILS"
        
    //         default:
    //             return val
    //     }
    // }




    /**
     * --------------------------------------------------------------------------
     * EG DynamicEditorStore.js
     * --------------------------------------------------------------------------
    */

    /**
     * @class DynamicEditorStore
     * @description A class for managing dynamic states associated with cells in a table editor.
     * @example
     * const editorStore = new DynamicEditorStore();
     * editorStore.setState(cell, startValue);
     * 
     */
    class DynamicEditorStore {
        constructor() {
            this.states = new Map();
            this.callbackQueue = [];
            this.processingQueue = false;//needed?
        }
        /**
         * @method cellToKey
         * @description Converts the cell object into a string key.
         * @param {Object} cell - The cell object with {columnName, rowKey, page, tableName}.
         * @returns {string} - The string key representing the cell object.
         */
        cellToKey(cell) {
            if (!this.validateCellObject(cell)) {
            return null;
            }
            if(typeof cell === 'object'){
                const { columnName, rowKey, table } = cell;
                return `${columnName}_${rowKey}_${table}`;
            }
            if(typeof cell === "string"){
                return cell;
            }
        }

        /**
         * @method setState
         * @description Sets the initial state for a given cell.
         * @param {Object} cell - The cell object with {columnName, rowKey, page, tableName}.
         * @param {string} startValue - The initial value for the cell.
         */
        setState(cell, startValue) {
            if (!this.validateCellObject(cell)) {
                return;
            }
            this.states.set(this.cellToKey(cell) , {
                startValue,
                nextValue: startValue,
                prevValue: null,
                lastValidValue: null,
                isValid: false,
                errorMessage: '' 
            });
        }

        /**
         * @method logState
         * @description Loges the state Object.
         */
        logState(msg) {
            const statesCopy = new Map([...this.states]);
            console.log(msg||'Log State: no msg provided')
            console.log(statesCopy)
            
        }
                
        
        /**
         * @method getStateValidity
         * @description Checks if the current state for a given cell is valid.
         * @param {Object} cell - The cell object with {columnName, rowKey, page, tableName}.
         * @returns {Object|null} - Object with isValid, errorMessage, and lastValidValue properties, or null if the state does not exist.
         */
        getStateValidity(cell) {
        if (!this.validateCellObject(cell)) {
            return null;
        }

        const state = this.states.get(this.cellToKey(cell));
        return state
            ? {
                isValid: state.isValid,
                errorMessage: state.errorMessage,
                lastValidValue: state.lastValidValue,
                nextValue:state.nextValue
            }
            : null;
        }

        /**
         * @method updateState
         * @description Updates the state with validation and queues callbacks.
         * @param {Object} cell - The cell object with {columnName, rowKey, page, tableName}.
         * @param {string} nextValue - The new value to be set.
         * @param {boolean} useInitial - Flag to determine if the initial state should be used as fallback.
         * @returns {Function|null} - Callback to validate the new state or null if the state does not exist.
         */
        updateState(cell, nextValue, useInitial = false) {
            if (!this.validateCellObject(cell)) {
                return null;
            }
            const state = this.states.get(this.cellToKey(cell));

            if (state) {
                const validateState = (isValid, message) => {
                    const callback = () => {

                        state.isValid = isValid;
                        state.errorMessage = isValid ? '' : message || 'Validation failed.';
                        state.prevValue = state.nextValue;
                        state.nextValue = nextValue;
                        if (isValid) {
                            state.lastValidValue = nextValue;
                        } 
                        this.states.set(this.cellToKey(cell), { ...state});
                    };

                    this.callbackQueue.push(callback);
                    this.processQueue();


                    return useInitial ? { ...state, prevValue: state.startValue} : { ...state };
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
         * @param {Object} cell - The cell object with {columnName, rowKey, page, tableName}.
         */
        destroyState(cell) {
        if (!this.validateCellObject(cell)) {
            return;
        }
    
        this.states.delete(this.cellToKey(cell));
        }

        /**
         * @method readState
         * @description Reads the state with access to first value, last valid value, and current value.
         * @param {Object} cell - The cell object with {columnName, rowKey, page, tableName}.
         * @returns {Object|null} - Object with firstValue, currentValue, isValid, errorMessage, and lastValidValue properties, or null if the state does not exist.
         */
        readState(cell) {
        if (!this.validateCellObject(cell)) {
            return null;
        }
    
        const state = this.states.get(this.cellToKey(cell));
    
        return state ? {...state} : null;
        }

        /**
         * @method validateCellObject
         * @description Validates the cell object.
         * @param {Object|String} cell - The cell object to validate.
         * @throws {Error} Throws an error if the cell object is invalid.
         * @returns {boolean} - True if the cell object is valid, otherwise false.
        */
        validateCellObject(cell) {
        if (!cell || (typeof cell !== 'object' && typeof cell !== 'string')) {
            console.error('Invalid cell object.');
            return false;
        }
        return true;
        }
    }

    const VALIDATOR = new DynamicEditorStore()
      



    /**
     * --------------------------------------------------------------------------
     * EG Sidebar.js
     * --------------------------------------------------------------------------
    */
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

        // // Additional Features

        // // Example: Toggle submenu
        // toggleSubmenu(target) {
        //     $(target).toggleClass('show-submenu');
        // }

        // // Example: Load dynamic content
        // loadDynamicContent() {
        //     // Example: Fetch data from an API and update the sidebar dynamically
        //     // ...
        // }

        // // Example: Show user profile
        // showUserProfile() {
        //     // Example: Display user profile information
        //     // ...
        // }

        // // Example: Toggle theme
        // toggleTheme() {
        //     // Example: Switch between light and dark themes
        //     // ...
        // }

        // // Example: Enable drag-and-drop functionality
        // enableDragAndDrop() {
        //     // Example: Implement drag-and-drop functionality for sidebar items
        //     // ...
        // }
      
    
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

    // Additional Event Handlers

    // EventHandler.on('.submenu-toggle', 'click', function () {
    //     x.toggleSubmenu(this);
    // });

    // EventHandler.on('#load-content-button', 'click', function () {
    //     x.loadDynamicContent();
    // });

    // EventHandler.on('#user-profile-button', 'click', function () {
    //     x.showUserProfile();
    // });

    // EventHandler.on('#toggle-theme-button', 'click', function () {
    //     x.toggleTheme();
    // });

    // EventHandler.on('.draggable-item', 'mousedown', function () {
    //     x.enableDragAndDrop();
    // });
    



    class TUITABLE {
        constructor(props){
            // this.el = document.getElementById('grid')
            // this.validator = props.validator
            this.PAGE = props.page
            this.API_PATH = '../assets/includes/api.php'
            this.TABLES=[]
            this.DROPDOUNS={}
            this.CURRENT=0
            this.DATE_RANGE='ds=2023-01-01&de=2023-12-31'
            this.MODAL = []
            this.MODALINDEX = 0
            this.config = {
                el:document.getElementById('grid'),
                bodyHeight: 'fitToParent',
                columns: [],
                data:[]
            }
            this.GRID = new tui.Grid(this.config)
            this.applyGridTheme()
            this.start()
            this.addEventListeners()


            console.log(this);
            VALIDATOR.logState('here')
        }




        //helpers
        urlBuilder(ep='',other=[]){
            var url = '';
            if(other.length > 0){
                other.forEach(e=>{url = url+'&'+e})
            }
            return '../assets/includes/api.php?ep='+ep+url
        }

        saveStateToLocalStorage() {
            const stateToSave = {
                currentTableIndex: this.CURRENT,
                dateRange: this.DATE_RANGE,
                // Add more state variables as needed
            };
            localStorage.setItem(`${this.PAGE}AppState`, JSON.stringify(stateToSave));
        }

        loadStateFromLocalStorage() {
            const savedState = localStorage.getItem(`${this.PAGE}AppState`);
            
            if (savedState) {
                const parsedState = JSON.parse(savedState);
                // Restore state variables
                this.CURRENT = parsedState.currentTableIndex;
                this.DATE_RANGE = parsedState.dateRange;
                // Add more state variables as needed
                this.setActiveTab()
            }
        }


        setActiveTab(activeEl = false){
            $('#myTab button').removeClass('active')
            if(activeEl){
                activeEl.addClass('active')
            } else{
                $(`#myTab button[data-index='${this.CURRENT}']`).addClass('active')
            }
            

        }

        applyGridTheme() { 
            window.tui.Grid.applyTheme('default', {
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
            });
        }

        setTableData(index){
            var _this = this
            console.log('table='+this.TABLES[index].name)
            $.ajax({
                type: "GET",
                url: _this.urlBuilder('get_table',['table='+this.TABLES[index].name,this.DATE_RANGE]),
                success: function(response) {
                    _this.GRID.resetData(response.data)
                },
                error: function(error) {
                    _this.GRID.resetData([])
                    // console.error(error);
                }
            });
        }

        setTable(index = this.CURRENT){
            // VALIDATOR.logState()
            var _this = this 
            _this.GRID.setColumns(_this.TABLES[index].columns)
            // set the data
            _this.setTableData(index)
            //add the modue for new data
            while (_this.MODAL.length > 0) {
                VALIDATOR.destroyState( _this.MODAL.shift()+_this.TABLES[_this.MODALINDEX].name) 
            }
            $.get('includes/modal_api.php?modal='+_this.TABLES[index].modal,res=>{
                $('#modal_go_here').empty().append(res).find('input').each(function() {
                    _this.MODAL.push($(this).attr('name'))
                    VALIDATOR.setState($(this).attr('name')+_this.TABLES[_this.CURRENT].name,'')
                    
                })
            })
            this.MODALINDEX = index
            // add new modales for new data
        }

        generateDynamicTabs(){
            this.TABLES.forEach((e,i)=>{
                $('#myTab').append(
                    `<li class="nav-item">
                        <button 
                            id="${e.name}_tab" 
                            data-index="${i}" 
                            class="nav-link ${i===0 ?'active':''}" 
                            type="button" role="tab" >
                            ${e.display_name}
                        </button>
                    </li>`)
            })
        }

        columnFunctions(name){
            var _this = this
            const functions ={
                FORMATCURRENCY : function(val){
                    // console.log(val);
                    if(_this.DROPDOUNS.fx && _this.DROPDOUNS.fx.length > 0){
                        var item = _this.DROPDOUNS.fx.find(item => item.id === val)
                        console.log(item);
                        return item.currency
                    }
                    else{
                     // return 'dadad'
                        switch (val.value) {
                            case 2:
                            case "2":
                                return "USD"
                            case 3:
                            case "3":
                                return "EUR"
                            case 4:
                            case "4":
                                return "ILS"
                            default:
                                return val.value
                        }
                    }
                }
            }
            return functions[name]
        }
        applyFunctions(){
            var _this = this
            _this.TABLES.forEach(tble=>{
                // console.log(tble);
                tble.columns.forEach(column => {
                    if(column.name === "currency"){
                        console.log('re');
                        column.formatter = _this.columnFunctions(column.formatter)
                    }
                });
            })
        }
        async getDropdouns(){
            var _this = this
            await $.get(this.urlBuilder('getDropdouns',['page='+this.PAGE]),function(res){
                    console.log(res);
                    if(res.data){
                        _this.DROPDOUNS = {..._this.DROPDOUNS,...res.data}
                    }
                    // res.data.forEach(e=>{
                    //     _this.DROPDOUNS.push(e)

                    // })
                    console.log(_this.DROPDOUNS);
            })
        }
        async start(){
            var _this = this
            // await this.getDropdouns()
            await $.get(this.urlBuilder('getAllTables',['page='+this.PAGE]),function(res){
                _this.TABLES = res.data
                _this.applyFunctions()
                $('#myTab').empty()
                _this.generateDynamicTabs()
                _this.loadStateFromLocalStorage()
                _this.setTable(_this.CURRENT)
            })
        }

        validateForm() {
            var _this = this
            var isValid = true;
        
            // Validate each form field
            $("#form_new_record input").each(function() {
                _this.validateField($(this));
                if ($(this).siblings(".error-message").text() !== "") {
                    isValid = false;
                }
            });
        
            return isValid;
        }

        validateField(field) {
            var _this = this
            var value = field.val().trim();
            var fieldName = field.attr("name");
            // console.log(fieldName+': '+value);
            var validatorCB =VALIDATOR.updateState(fieldName+_this.TABLES[_this.CURRENT].name,value)
            // Send an AJAX request to the validation endpoint
            $.ajax({
                type: "POST",
                url: _this.API_PATH+"?ep=validate_field&table="+_this.TABLES[_this.CURRENT].name, // Replace with your actual validation endpoint
                data: { fieldName: fieldName, value: value },
                success: function(response) {
                    var validityState = validatorCB(response.data.isValid,response.data.message)
                    // console.log(response);
                    if(validityState.isValid){
                        field.removeClass('is-invalid').addClass('is-valid').siblings(".error-message").text(validityState.errorMessage)
                    }else{
                        // Display the error message returned from the server
                        field.removeClass('is-valid').addClass('is-invalid').siblings(".error-message").text(validityState.errorMessage);
                    }
                },
                error: function(error) {
                    // Handle errors
                    console.error(error);
                }
            });
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
            $.ajax({
                type: "POST",
                url: _this.API_PATH+'?ep=new_row&table='+_this.TABLES[_this.CURRENT].name, // Replace with your actual PHP script URL
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

        addEventListeners(){
            var _this = this
            // console.log(this.GRID);
            // this.GRID.on('click',ev=>{
            //     console.log(ev);
            // })

            // grid clear filters
            // $('#clear_Filetrs').on('click',()=>{
            //     _this.TABLES[_this.CURRENT].columns.forEach(e=>{
            //         _this.GRID.unfilter(e.name)
            //     })
            // })

            // set tabs to active
            // $('#myTab').on('click','button',function(){
            //     _this.CURRENT = $(this).attr('data-index')
            //     _this.setActiveTab($(this))
            //     _this.setTable(_this.CURRENT)
            // })

            //detect modal closing and remove all data from it 
            $(document).on('hide.bs.modal','#basicModal',function(){
                $(this).find('input').each(function(input){
                    $(this).val('').removeClass(['is-valid','is-invalid']).siblings('.error-message').text('')
                })
            })
            

            // validate modal input changes
            $(document).on("input change","#form_new_record input, #form_new_record select",function() {
                _this.validateField($(this));
            });
              
            //on click in cell 
            // this.GRID.on('editingStart',function(e) {
            //     console.log(e);
            //     var {columnName,rowKey,value} = e
            //     VALIDATOR.setState({columnName,rowKey,table:_this.TABLES[_this.CURRENT].name},value)
            // })

            $(document).on('click', '.tui-select-box-item',function(e){
                var  currentCell = _this.GRID.getFocusedCell()
                var {columnName,rowKey } = currentCell
                var cell = {columnName,rowKey,table:_this.TABLES[_this.CURRENT].name}
                var validatorCB =VALIDATOR.updateState(cell,e.target.dataset.value)

                $.ajax({
                    type: "POST",
                    url: _this.urlBuilder('validate_field',["table="+_this.TABLES[_this.CURRENT].name]),
                    data: { fieldName: columnName, value: e.target.dataset.value},
                    success: function(response) {
                        validatorCB(response.data.isValid,response.data.message)
                    },
                    error: function(error) {
                        // Handle errors
                        console.error(error);
                    }
                });

            })
            // once enter these 3 fire in order
            this.GRID.on('beforeChange',function(ev) {
                console.log(ev);
                var {columnName,rowKey} = ev.changes[0]
                var cell ={columnName,rowKey,table:_this.TABLES[_this.CURRENT].name}
                // VALIDATOR.logState()
                var valid = VALIDATOR.getStateValidity(cell)
                if(valid && !valid.isValid){
                    ev.stop()
                    VALIDATOR.destroyState(cell)
                    toastr.error(valid.errorMessage, 'Validation Error!')
                }
            })

            this.GRID.on('afterChange',function(ev){
                console.log(ev);
                var {columnName,rowKey} = ev.changes[0]
                var row = _this.GRID.getRow(rowKey)
                // console.log(row);
                var cell = {columnName,rowKey,table:_this.TABLES[_this.CURRENT].name}
                var valid = VALIDATOR.getStateValidity(cell)
                if(valid && valid.isValid){
                    // _this.GRID.request('updateData',{
                    //     showConfirm:false,
                    //     url: _this.urlBuilder(_this.TABLES[_this.CURRENT].uris.updateData,['table='+_this.TABLES[_this.CURRENT].name]),
                    //     method: 'POST'
                    // })
                    $.ajax({
                        type: "POST",
                        url: _this.urlBuilder('update_table',["table="+_this.TABLES[_this.CURRENT].name]),
                        data: { fieldName: columnName, value: valid.nextValue,id:row.id },
                        success: function(response) {
                            console.log(response);
                            window.toastr.info('Success')
                            VALIDATOR.destroyState(cell)
                        },
                        error: function(error) {
                            // Handle errors
                            console.error(error);
                        }
                    })
                    VALIDATOR.destroyState(cell)
                }
            })

            $(document).on("input","#grid input.tui-grid-content-text",function(e) {
                var  currentCell = _this.GRID.getFocusedCell()
                var {columnName,rowKey } = currentCell
                var cell = {columnName,rowKey,table:_this.TABLES[_this.CURRENT].name}
                var input = $(this)
                var validatorCB =VALIDATOR.updateState(cell,e.target.value)
                $.ajax({
                    type: "POST",
                    url: _this.urlBuilder('validate_field',["table="+_this.TABLES[_this.CURRENT].name]),
                    data: { fieldName: cell.columnName, value: e.target.value },
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

            $(document).on("click","#submit_btn_new_record",function(){
                // Validate all fields before submitting
                if (_this.validateForm()) {
                    // If the form is valid, send the data to the server
                    _this.sendDataToServer();
                }
            });
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
        TUITABLE,
        VALIDATOR
    }
    // expose EG to window/global
    global.EG = out



})(window, $)



