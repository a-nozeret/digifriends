let ReadQuery = React.createClass({
    // Render
    render: function() {
        // Don't render if no selected value
        if (this.props.selectedValue === '') {
            return null
        }

        // Build list of digifriends
        let digifriends;
        if (this.props.digifriends.length > 0) {
            digifriends = this.props.digifriends.map(function(d, i) {
                return (<li className="o-digifriend" key={i}>{d.value}</li>);
            }.bind(this));
        }
        else {
            digifriends = (<li className="o-digifriend o-digifriend--no-friend"><i className="fa fa-exclamation"></i></li>);
        }

        // Return selected query View
        return (
            <div className="c-selected">
                <span className='c-query'>{this.props.selectedValue}</span>
                {digifriends ? <ul className="c-digifriends">{digifriends}</ul> : ''}
            </div>
        );
    }
});

let MainApp = React.createClass({

    // Initial values
    getInitialState: function() {
        return {
            queries: [],
            value: '',
            selectedValue: '',
            digifriends: []
        };
    },

    // Component did mount
    componentDidMount: function() {
        this.resetList();
        $('.c-new-query__input').focus();
        $('body').removeClass('loading');
        if (this.state.selectedValue !== null) {
            this.resetList(function (q) {
                // Select the latest query if there is one
                if (q.length > 0) {
                    this.state.selectedValue = q[0].value;
                    this.selectQuery();
                }
            }.bind(this));
        }
    },

    // Component will unmount
    componentWillUnmount: function() {
        this.serverRequest.abort();
    },

    // Event handlers
    onValueChange: function(e) {
        if (this.isValidValue(e.target.value)) {
            this.setState({value: e.target.value});
        }
        else {
            e.target.value = this.state.value;
        }
    },
    onSubmit: function(e){
        $.post("api/post.php", {
                value: this.state.value
            },
            function(data){
                this.setState({selectedValue: this.state.value});
                this.selectQuery();
                this.resetList();
                this.setState({value: ''});
            }.bind(this)
        );
        e.preventDefault();
    },

    // Actions
    resetList: function (callback) {
        this.serverRequest = $.get("api/list.php", function (queries) {
            this.setState({ queries: queries });
            callback = callback ? callback(queries): '';

        }.bind(this));
    },
    onPreviousQueryClick: function (e) {
        this.state.selectedValue = $(e.target).text();
        this.selectQuery();
        this.resetList();
    },
    onPreviousQueriesToggle: function (e) {
        $('.c-queries').toggleClass('toggled')
    },
    selectQuery: function () {
        this.serverRequest = $.post("api/index.php",
            { selected_value: this.state.selectedValue },
            function (data) {
                if (data && data.query.length > 0) {
                    this.setState({digifriends: data.digifriends});
                }
            }.bind(this)
        );
    },

    // Utils
    isValidValue: function (x) {
        return !isNaN(x) && (0 <= x && x <= 1000);
    },

    // Render
    render: function() {
        let previousQueries = this.state.queries
            .map(function(q, i) {
                return (
                    <span className="o-previous-query" key={i} onClick={this.onPreviousQueryClick}>{q.value}</span>
                );
            }.bind(this));
        return (
            <div className="c-main">
                <form onSubmit={this.onSubmit} className="c-new-query">
                    <label htmlFor="value" className="c-new-query__label">
                        Enter a number
                        <i className="fa fa-arrow-right"></i>
                    </label>
                    <input
                        name="value"
                        type="number"
                        className="c-new-query__input"
                        value={this.state.value}
                        placeholder="..."
                        onChange={this.onValueChange}/>

                    <input
                        type="submit"
                        value="Check its Digifriends"
                        className="c-new-query__button"
                        onClick={this.onSubmit}/>
                </form>

                <div className="c-queries">
                    <h3 className="c-queries__title" onClick={this.onPreviousQueriesToggle}>
                        <i className="fa fa-history"></i>
                        Previous queries
                    </h3>
                    <div className="c-queries__elements">
                        {previousQueries}
                    </div>
                </div>

                <ReadQuery selectedValue={this.state.selectedValue} digifriends={this.state.digifriends}/>
            </div>
        );
    }
});

let App = React.createClass({
    render: function() {
        return <MainApp />;
    }
});

ReactDOM.render(
    <App />,
    document.getElementById('root')
);
