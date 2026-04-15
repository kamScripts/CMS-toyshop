 const CONFIG = {
    API_BASE: window.location.hostname.includes('localhost')
     ? 'http://localhost/CMS-toyshop/server/'
        : '/server/'
};
export default CONFIG;