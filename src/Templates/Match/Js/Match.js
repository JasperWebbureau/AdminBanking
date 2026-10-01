class AdminBankingMatch {
    constructor(root=document){this.root=root;this.timer=null;this.delay=350;this.onInput=this.onInput.bind(this);this.onClick=this.onClick.bind(this);root.addEventListener('input',this.onInput);root.addEventListener('click',this.onClick);}
    onInput(event){const field=this.closest(event.target,'[data-admin-banking-manual-search] input[name="q"]');if(!field){return;}window.clearTimeout(this.timer);this.timer=window.setTimeout(()=>this.submit(field),this.delay);}
    onClick(event){const button=this.closest(event.target,'[data-admin-banking-show-expense]');if(!button){return;}const panel=this.root.querySelector('[data-admin-banking-expense-panel]');if(!panel){return;}panel.hidden=false;button.setAttribute('aria-expanded','true');panel.scrollIntoView({behavior:'smooth',block:'start'});const title=panel.querySelector('input[name="title"]');if(title){title.focus({preventScroll:true});}}
    submit(field){const form=field.closest('form');if(!form||!form.isConnected){return;}if(typeof form.requestSubmit==='function'){form.requestSubmit();return;}form.dispatchEvent(new Event('submit',{bubbles:true,cancelable:true}));}
    closest(target,selector){return target instanceof Element?target.closest(selector):null;}
}
if(!window.adminBankingMatch){window.adminBankingMatch=new AdminBankingMatch();}
