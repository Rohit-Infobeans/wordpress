function escape(string) {
                return string ? String(string).replace(/[&<>"']/g, function(match) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    }[match];
                }) : '';
            }

            $(document).ready(function() {

                // [{ id: 'Amsterdam', timezone: '+01:00' }, ...]
                var citiesWithTimezone = $('#multiple-select-box').find('option').map(function() {
                    return {
                        id: this.textContent,
                        timezone: this.getAttribute('data-timezone')
                    };
                }).get();

                var transformText = $.fn.selectivity.transformText;

                // example query function that returns at most 10 cities matching the given text
                function queryFunction(query) {
                    var selectivity = query.selectivity;
                    var term = query.term;
                    var offset = query.offset || 0;
                    var results;
                    if (selectivity.$el.attr('id') === 'single-input-with-submenus') {
                        var timezone = selectivity.dropdown.highlightedResult.id;
                        results = citiesWithTimezone.filter(function(city) {
                            return transformText(city.id).indexOf(transformText(term)) > -1 &&
                                   city.timezone === timezone;
                        }).map(function(city) { return city.id });
                    } else {
                        results = cities.filter(function(city) {
                            return transformText(city).indexOf(transformText(term)) > -1;
                        });
                    }
                    results.sort(function(a, b) {
                        a = transformText(a);
                        b = transformText(b);
                        var startA = (a.slice(0, term.length) === term),
                            startB = (b.slice(0, term.length) === term);
                        if (startA) {
                            return (startB ? (a > b ? 1 : -1) : -1);
                        } else {
                            return (startB ? 1 : (a > b ? 1 : -1));
                        }
                    });
                    setTimeout(function() {
                        query.callback({
                            more: results.length > offset + 10,
                            results: results.slice(offset, offset + 10)
                        });
                    }, 500);
                }

                

                $('#multiple-input').selectivity({
                    multiple: true,
                    placeholder: 'Type to search cities',
                    query: queryFunction
                });

                $('#tags-input').selectivity({
                    items: ['red', 'green', 'blue'],
                    multiple: true,
                    tokenSeparators: [' '],
                    value: ['brown', 'red', 'green']
                });

                              $('#multiple-select-box').selectivity();
            });