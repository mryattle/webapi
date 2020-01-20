class Utils {
    static uuidv4() {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
          return v.toString(16);
      });
    }             
}

class HttpClient {
    constructor() {
      // Webhook
      this.api_key = Utils.uuidv4();
      this.api_secret = 'dCJ6rP9Ztvd3a5fakJujPBaXRBZ9cT2N';
      this.unix_timestamp = new Rx.ReplaySubject(1);
      this._timestamp = null;
      this.initUnixTimestamp();

      this.session_id = Utils.uuidv4();
      this.token = new Rx.ReplaySubject(1);
      this.uuid = new Rx.ReplaySubject(1);
      this._current_token = null;
      this.newJWT();
    }               
    newRequestUuid() {
      return Utils.uuidv4();
    }    
    renewUuid() {
      this.uuid.onNext(this.newRequestUuid());
    }   
    newJWT() {
      fetch(`/api/login`, {
        method: "POST",
        dataType: "JSON",
        headers: {
          "Content-Type": "application/json; charset=utf-8",
        },
        body: JSON.stringify({
        'email': 'test@laravel', 
        'password': '12345', })
      })
      .then((resp) => { 
        return resp.json();
      }) 
      .then((data) => {
        // Here we can renew toke using expire attribute
        const self = this;
        this._current_token = data.token;
        setInterval(function(){ self.renewJWT(); }, ((parseInt(data.expires)/2)*1000));
        this.token.onNext(data.token);               
      })
      .catch((error) => {
        alert(error.message);
      });                
    }
    renewJWT() {
      fetch(`/api/refresh`, {
        method: "POST",
        dataType: "JSON",
        headers: {
          "Content-Type": "application/json; charset=utf-8",
          "Authorization": `Bearer ${this._current_token}`,
        }
      })
      .then((resp) => { 
        return resp.json();
      }) 
      .then((data) => {
        // Here we can renew toke using expire attribute
        this._current_token = data.token;
        this.token.onNext(data.token);               
      })
      .catch((error) => {
        alert(error.message);
      });               
    }  
    initUnixTimestamp() {
      // It's really import to be syncd with server clock
      // huge time differences can lead to serve Replay-Attack detection 
      fetch(`/api/webhook/timestamp`, {
        method: "GET",
        dataType: "JSON",
        headers: {
          "Content-Type": "application/json; charset=utf-8",
        }
      })
      .then((resp) => { 
        return resp.json();
      }) 
      .then((data) => {
        // Here we can renew toke using expire attribute
        this._timestamp = data.unix_timestamp;
        this.unix_timestamp.onNext(data.unix_timestamp);  
        // Renew secret on webhook HMAC expiration period (milleconds)
        const self = this;
        setInterval(function(){ self.newUnixTimestamp(); }, ((parseInt(data.maxInterval)/2)*1000));                             
      })
      .catch((error) => {
        alert(error.message);
      });               
    }            
    newUnixTimestamp() {
      fetch(`/api/webhook/timestamp`, {
        method: "GET",
        dataType: "JSON",
        headers: {
          "Content-Type": "application/json; charset=utf-8",
        }
      })
      .then((resp) => { 
        return resp.json();
      }) 
      .then((data) => {
        // Here we can renew toke using expire attribute
        this._timestamp = data.unix_timestamp;
        this.unix_timestamp.onNext(data.unix_timestamp);                             
      })
      .catch((error) => {
        alert(error.message);
      });  
    }
    hmacSignature(timestamp, method, url, payload) {
      try {
        let hmac = new jsSHA('SHA-512', 'TEXT');
        hmac.setHMACKey(this.api_secret,'TEXT');
        hmac.update(timestamp);
        hmac.update(method);
        hmac.update(url);
        hmac.update(md5(payload));
        return hmac.getHMAC('HEX')
      } catch(e) {
        return e.message
      }    
    }          
}

const http_client = new HttpClient();