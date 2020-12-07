function AsyncEventBus() {
  this.listeners = {};
  this.oneTimeListeners = {};
}

AsyncEventBus.prototype.on = function on(name, scope, callback = null) {
  return _assign(name, scope, callback, this.listeners);
};

AsyncEventBus.prototype.once = function once(name, scope, callback = null) {
  return _assign(name, scope, callback, this.oneTimeListeners);
};

AsyncEventBus.prototype.emit = async function emit(name) {
  let result;

  result = _emit(this.listeners[name]);
  if (result === false) return result;

  result = _emit(this.oneTimeListeners[name], true);
  if (result === false) return result;

  return result;
};

AsyncEventBus.prototype.off = function off(name, fn) {
  _destroy(this.listeners[name], fn);
  _destroy(this.oneTimeListeners[name], fn);
};

const _assign = (name, scope, callback, arr) => {
  if (callback === null && typeof scope === "function") {
    callback = scope;
    scope = null;
  }
  if (!Array.isArray(arr[name])) {
    arr[name] = [];
  }
  // Bind the scope
  callback.bind(scope);
  arr[name].push(callback);
};

const _emit = async (listeners = [], remove = false) => {
  let result = null;
  for (var i = 0; i < listeners.length; i++) {
    if (typeof listeners[i] === "function") {
      result = await listeners[i]();
      if (remove) listeners.splice(i, 1);
      if (result === false) return result;
    }
  }
  return result;
};

const _destroy = (listeners = [], fn = () => {}) => {
  for (var i = 0; i < listeners.length; i++) {
    if (listeners[i] === fn) {
      listeners.splice(i, 1);
    }
  }
};

export default new AsyncEventBus();
