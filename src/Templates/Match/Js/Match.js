class AdminBankingMatch {
    constructor(root=document){this.root=root;this.timer=null;this.delay=350;this.onInput=this.onInput.bind(this);root.addEventListener('input',this.onInput);}
    onInput(event){const field=this.closest(event.target,'[data-admin-banking-manual-search] input[name="q"]');if(!field){return;}window.clearTimeout(this.timer);this.timer=window.setTimeout(()=>this.submit(field),this.delay);}
    submit(field){const form=field.closest('form');if(!form||!form.isConnected){return;}if(typeof form.requestSubmit==='function'){form.requestSubmit();return;}form.dispatchEvent(new Event('submit',{bubbles:true,cancelable:true}));}
    closest(target,selector){return target instanceof Element?target.closest(selector):null;}
}
if(!window.adminBankingMatch){window.adminBankingMatch=new AdminBankingMatch();}
