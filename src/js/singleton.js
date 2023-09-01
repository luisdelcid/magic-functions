class __Singleton {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static instances = [];
	static is_internal_constructing = false;

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return this
	 */
	static get_instance(){
        if('object' === typeof(this.instances[this.name])){
            return this.instances[this.name];
        }
		this.is_internal_constructing = true;
		this.instances[this.name] = new this;
		this.is_internal_constructing = false;
		return this.instances[this.name];
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	/**
	 * @return void
	 */
	constructor(){
		if(this.constructor.is_internal_constructing){
            var l10n = this.constructor.name.toLowerCase() + '_l10n';
            this.l10n = {};
            if('object' === typeof(window[l10n])){
                this.l10n = window[l10n];
            }
			if('function' === typeof(this.load)){
				this.load();
			}
		} else {
			throw new TypeError('This class is not constructable.');
        }
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}
