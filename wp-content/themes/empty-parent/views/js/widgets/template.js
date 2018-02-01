var tForm = (function () {
    return new Vue({
        el: '#templates-form',
        data: {
            items: items,
            state: null,
            asc: true,
            result: null
        },
        created: function () {
            this.state = jQuery.makeArray(this.items).map(function (index, value) {
                return 'idle';
            });
        },
        updated: function () {
            self.result = null;
        },
        methods: {
            updateState: function () {
                this.state = jQuery.makeArray(this.items).map(function (index, value) {
                    return 'idle';
                });
            },
            ajax: function (action, params) {
                var self = this;
                jQuery.post(admin_ajax, {
                    nonce: nonce,
                    params: params,
                    what: action,
                    action: 'template_list'
                }, function (result) {
                    self.result = result;
                });
            },
            db: function (action, params) {
                var self = this;
                switch (action) {
                    case 'add':
                        return new Promise(function (resolve, reject) {
                            self.ajax(action);
                            var checkResult = setInterval(function () {
                                if (self.result !== null && self.result !== undefined) {
                                    resolve(self.result);
                                    clearInterval(checkResult);
                                }
                            }, 1000);

                        });
                        break;
                    case 'delete':
                        return new Promise(function (resolve, reject) {
                            self.ajax(action, params)
                            var checkResult = setInterval(function () {
                                if (self.result !== null && self.result !== undefined) {
                                    resolve(self.result);
                                    clearInterval(checkResult);
                                }
                            }, 1000);
                        });
                        break;
                    case 'edit':
                        return new Promise(function (resolve, reject) {
                            self.ajax(action, params)
                            var checkResult = setInterval(function () {
                                if (self.result !== null && self.result !== undefined) {
                                    resolve(self.result);
                                    clearInterval(checkResult);
                                }
                            }, 1000);
                        });
                        break;
                }
            },
            addNew: function () {
                var self = this;
                this.db('add').then(function (result) {
                    self.items.push({name: result, id: result});
                    self.updateState();
                    self.$forceUpdate();
                });
            },
            edit: function (index) {
                this.state[index] = 'edit';
                this.$forceUpdate();
            },
            submit: function (index, event) {
                this.state[index] = 'sending';
                self = this;
                this.db('edit', {
                    id: self.items[index].id,
                    name: event.srcElement.previousElementSibling.value
                }).then(function (result) {
                    if (result) {
                        self.items[index].name = event.srcElement.previousElementSibling.value;

                    }
                    self.state[index] = 'idle';
                    self.$forceUpdate();
                });
            },
            idleState: function (index) {
                return this.state[index] == 'idle';
            },
            editState: function (index) {
                return this.state[index] == 'edit';
            },
            sortItems: function () {
                this.asc = !this.asc;
                var sorted = this.items.sort(function (a, b) {
                        return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);
                    }
                );

                if (!this.asc) {
                    sorted = sorted.reverse();
                }

                this.items = sorted;
                this.$forceUpdate();
            },
            bulk: function () {
                var bulk = this.$refs.bulk;
                switch (bulk.value) {
                    case 'bulk-delete':
                        this.deleteItems();
                        break;
                }
                this.$forceUpdate();
            },
            deleteItems: function () {
                var deleting = jQuery.makeArray(jQuery('[name="bulk-delete[]"]:checked').map(function (value) {
                    return jQuery(this).val();
                }));
                self = this;
                self.db('delete', deleting).then(function (result) {
                    if (result) {
                        var place = self.items.filter(
                            function (item) {
                                return deleting.indexOf(item.id) > -1;
                            }
                        );
                        place.map(function (value) {
                            self.items.splice(
                                self.items.indexOf(value),
                                1
                            );
                        });

                    }
                    self.$refs.bulk.value = -1;
                    jQuery('[name="bulk-delete[]"]:checkbox:enabled').prop('checked', false);
                });
            }
        }
    });
})();
