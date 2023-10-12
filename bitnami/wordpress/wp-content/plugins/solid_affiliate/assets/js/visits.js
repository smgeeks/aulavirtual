///////////////////////////////////////////////////////////
// Solid Affiliate v1.2.0
// SolidAffiliate JS
window.SolidAffiliate = {
  ajaxurl: sld_affiliate_js_variables.ajaxurl,
  affiliate_param: sld_affiliate_js_variables.affiliate_param,
  visit_cookie_key: sld_affiliate_js_variables.visit_cookie_key,
  visit_cookie_expiration_in_days:
    sld_affiliate_js_variables.visit_cookie_expiration_in_days,
  is_landing_pages_enabled: sld_affiliate_js_variables.landing_pages.is_landing_pages_enabled,
  is_home_page_a_landing_page: sld_affiliate_js_variables.landing_pages.is_home_page_a_landing_page,
  is_cookies_disabled: sld_affiliate_js_variables.is_cookies_disabled,

  get_affiliate_slug_from_url: function () {
    var url = window.location.href;
    var affiliate_param = this.affiliate_param;

    var url_string = window.location.href;
    var url = new URL(url_string);
    return url.searchParams.get(affiliate_param);
  },

  maybe_track_visit: function () {
    maybe_affiliate_slug = this.get_affiliate_slug_from_url();

    // Does query variable exist in url?
    is_slug_missing_from_url = (maybe_affiliate_slug == null || maybe_affiliate_slug == "");

    // check if current url is the home directory
    is_currently_on_home_page = (window.location.pathname == "/")

    if (this.is_cookies_disabled) {
      return;
    } 

    if (this.getCookie(this.visit_cookie_key) != null) {
      return;
    } else if (is_slug_missing_from_url && !this.is_landing_pages_enabled) {
      return;
    } else if (is_slug_missing_from_url && this.is_landing_pages_enabled && (!this.is_home_page_a_landing_page && is_currently_on_home_page)) {
      return;
    } else {
      var data = {
        action: "sld_affiliate_track_visit", // wp ajax action
        affiliate_slug: maybe_affiliate_slug,
        landing_url: window.location.href,
        http_referer: document.referrer,
        visit_ip: "",
        previous_visit_id: 0,
      };

      jQuery.ajax({
        url: this.ajaxurl, // this will point to admin-ajax.php
        type: "POST",
        data: data,
        success: function (response) {
          if (response["success"] && response["data"]["created_visit_id"]) {
            SolidAffiliate.setCookie(
              SolidAffiliate.visit_cookie_key,
              parseInt(response["data"]["visit_id"]),
              SolidAffiliate.visit_cookie_expiration_in_days
            );
          }
        },
      });
    }
  },

  setCookie: function (name, value, days) {
    if (this.is_cookies_disabled) {
      return;
    }
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  },

  getCookie: function (name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  },

  eraseCookie: function (name) {
    document.cookie =
      name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  },
};

jQuery(window).on("load", function () {
  if (window.SolidAffiliate && window.SolidAffiliate.maybe_track_visit) {
    window.SolidAffiliate.maybe_track_visit();
  }
});


