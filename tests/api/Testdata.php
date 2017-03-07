<?php

/**
 *
 * Testdata.php
 *
 * @author  sven
 * @created 10/20/2014 16:29
 */
class Testdata
{


    /**
     * Returns test data (fixture) for given resource
     *
     * @param $resourceName
     *
     * @return string
     */
    public function getTestObjectForPost($resourceName)
    {
        /**
         * Create getter method name from resource name
         */

        $getterName = 'get' . implode(array_map('ucwords', explode('/', $resourceName))) . 'ForPost';
        if (method_exists($this, $getterName)) {
            return $this->$getterName();
        }
        //return empty json object if getter method is not found
        return '{}';
    }

    /**
     * Returns JSON for a new system/role
     *
     * @return string
     */
    public function getSystemRoleForPost()
    {
        $json
            = <<<JSON
        [
    {
        "role_name": "Superadmins %s",
        "role_type": "G",
        "sort_order": "1",
        "tree_level": "1",
        "acl": [
            {
                "permission": "allow",
                "resource": "all"
            }
          ]
    }
  ]
JSON;
        $json = sprintf($json, mt_rand(1000, 9999));
        return $json;
    }

    /**
     * Returns JSON for a new system/user
     *
     * @return string
     */
    public function getSystemUserForPost()
    {
        $json
            = <<<JSON
   [
   {
        "email": "sven_%1\$s@mageflow.com",
        "firstname": "Demo%1\$s",
        "is_active": "1",
        "lastname": "Demo%1\$s",
        "reload_acl_flag": "0",
        "username": "demo%1\$s",
        "roles": [
        "Administrators"
        ]
    }
    ]
JSON;
        $json = sprintf($json, mt_rand(1000, 9999));
        return $json;
    }

    /**
     * Returns JSON for a new cms/block
     *
     * @return string
     */
    public function getCmsBlockForPost()
    {
        $json
            = <<<JSON
[
  {
        "content": "{{block type=\"enterprise_catalogevent/event_lister\" name=\"catalog.event.lister\" template=\"catalogevent/lister.phtml\"}}",
        "identifier": "a_new_block_%1\$s",
        "is_active": "1",
        "title": "New block's name %1\$s",
        "stores": [
            "admin"
        ]
    }
]
JSON;
        $json = sprintf($json, mt_rand(1000, 9999));
        return $json;
    }

    public function getCmsPageForPost()
    {
        $json
            = <<<JSON
[
             {
        "content": "testcontent %1\$s",
        "identifier": "my-page-%1\$s",
        "is_active": "1",
        "meta_description": "Page description %1\$s",
        "meta_keywords": "Page keywords %1\$s",
        "root_template": "two_columns_right",
        "sort_order": "0",
        "title": "My page title %1\$s",
        "updated_at": "2014-12-05 07:54:17",
        "stores": [
            "admin"
        ]
    }
]
JSON;

        $json = sprintf($json, mt_rand(1000, 9999));

        return $json;
    }

    /**
     * @return string
     * @apitype email/template
     */
    public function getEmailTemplateForPost()
    {
        $json
            = <<<JSON
[
   {
      "created_at": "2014-10-20 11:53:26",
      "mf_guid": "6c7ez07d76fnyqjo4mx8wea4zx27q8u5",
      "template_code": "Contact form (Eesti keeles)",
      "template_styles": "",
      "template_subject": "Contact form",
      "template_text": "Contact form {{config path=\"trans_email\/ident_custom1\/name\"}}",
      "template_type": 2,
      "updated_at": "2014-10-20T11:53:26+00:00"
    }
]
JSON;
        return $json;
    }





/*
*This function below is created by Peeter - data taken with Postman. Data called "content" left out.
*/
public function getSystemOrderStatusForPost()
    {
        $json = <<<JSON
[
   
    {
        "label": "Canceled",
        "status": "canceled",
        "store_labels": []
    },
    {
        "label": "Closed",
        "status": "closed",
        "store_labels": []
    },
    {
        "label": "Complete",
        "status": "complete",
        "store_labels": []
    },
    {
        "label": "Suspected Fraud",
        "status": "fraud",
        "store_labels": []
    },
    {
        "label": "On Hold",
        "status": "holded",
        "store_labels": []
    },
    {
        "label": "Payment Review",
        "status": "payment_review",
        "store_labels": []
    },
    {
        "label": "PayPal Canceled Reversal",
        "status": "paypal_canceled_reversal",
        "store_labels": []
    },
    {
        "label": "PayPal Reversed",
        "status": "paypal_reversed",
        "store_labels": []
    },
    {
        "label": "Pending",
        "status": "pending",
        "store_labels": []
    },
    {
        "label": "Pending Payment",
        "status": "pending_payment",
        "store_labels": []
    },
    {
        "label": "Pending PayPal",
        "status": "pending_paypal",
        "store_labels": []
    },
    {
        "label": "Processing",
        "status": "processing",
        "store_labels": []
    }

]
JSON;

         return $json;
    }






/*
*****************************This function is created by Peeter.
*/
    public function getCmsPollForPost()
    {
        $json = <<<JSON
[
  
    {
        "active": "1",
        "closed": "0",
        "date_posted": "2014-12-09 15:13:05",
        "poll_title": "What is your favorite color",
        "store": "admin",
        "stores": [
            "default"
        ],
        "answers": [
            {
                "answer_title": "Green",
                "answer_order": "0"
            },
            {
                "answer_title": "Red",
                "answer_order": "0"
            },
            {
                "answer_title": "Black",
                "answer_order": "0"
            },
            {
                "answer_title": "Magenta",
                "answer_order": "0"
            }
        ]
    },
    {
        "active": "1",
        "closed": "0",
        "date_posted": "2014-12-16 07:52:13",
        "poll_title": "Kuidas meeldib?",
        "store": "admin",
        "stores": [
            "default"
        ],
        "answers": [
            {
                "answer_title": "Meeldib",
                "answer_order": "0"
            },
            {
                "answer_title": "Ei meeldi",
                "answer_order": "0"
            }
        ]
    }

]
JSON;

         return $json;
    }



/*
*****************************This function is created by Peeter Tue 16 Dec 2014
*/
    public function getCmsWidgetForPost()
    {
        $json = <<<JSON
[
    
  {
        "instance_type": "cms/widget_page_link",
        "package_theme": "default/iphone",
        "title": "TestWidget",
        "widget_parameters": {
            "anchor_text": "",
            "title": "",
            "page": "uiuiuiuiuui"
        },
        "stores": [
            "admin"
        ]
    }

]
JSON;

         return $json;
    }


/*
***************************This function is created by Peeter.
*/
    public function getPromotionRuleCheckoutForPost()
    {
        $json = <<<JSON
[
    {
     




    }
]
JSON;

         return $json;
    }






    /*
**************************This function is created by Peeter.
*/
    public function getPromotionRuleCatalogForPost()
    {
        $json = <<<JSON
[
    {
     




    }
]
JSON;

         return $json;
    }


     /*
************************This function is created by Peeter.
*/
    public function getSalesTaxRuleForPost()
    {
        $json = <<<JSON
[
    {
        "calculate_subtotal": "0",
        "code": "Retail Customer-Taxable Goods-Rate 1",
        "position": "1",
        "priority": "1",
        "calculations": [
            {
                "tax_calculation_rate": "US-CA-*-Rate 1",
                "customer_tax_class": "Retail Customer",
                "product_tax_class": "Taxable Goods"
            },
            {
                "tax_calculation_rate": "US-NY-*-Rate 1",
                "customer_tax_class": "Retail Customer",
                "product_tax_class": "Taxable Goods"
            }
        ]
    }
]
JSON;

         return $json;
    }




     /*
********************This function is created by Peeter.
*/
    public function getSalesTaxRateForPost()
    {
        $json = <<<JSON
[
    {
        "code": "US-CA-*-Rate 1",
        "rate": "8.2500",
        "tax_country_id": "US",
        "tax_postcode": "*",
        "tax_region_id": "12",
        "titles": []
    },
    {
        "code": "US-NY-*-Rate 1",
        "rate": "8.3750",
        "tax_country_id": "US",
        "tax_postcode": "7-8",
        "tax_region_id": "43",
        "zip_from": "7",
        "zip_is_range": "1",
        "zip_to": "8",
        "titles": []
    },
    {
        "code": "yuyuyu",
        "rate": "5.0000",
        "tax_country_id": "US",
        "tax_postcode": "78-79",
        "tax_region_id": "0",
        "zip_from": "78",
        "zip_is_range": "1",
        "zip_to": "79",
        "titles": []
    }
]
JSON;

         return $json;
    }


    /*
********************This function is created by Peeter.
*/
    public function getSalesTaxClassProductForPost()
    {
        $json = <<<JSON
[
    {
        "class_name": "Taxable Goods",
        "class_type": "PRODUCT"
    },
    {
        "class_name": "Shipping",
        "class_type": "PRODUCT"
    }
]
JSON;

         return $json;
    }


     /*
********************This function is created by Peeter.
*/
    public function getSalesTaxClassCustomerForPost()
    {
        $json = <<<JSON
[
    {
        "class_name": "Retail Customer",
        "class_type": "CUSTOMER"
    }
]
JSON;

         return $json;
    }




     /*
********************This function is created by Peeter.
*/
    public function getCustomerGroupForPost()
    {
        $json = <<<JSON
[
   
    {
        "customer_group_code": "Retailer1",
        "tax_class": "Retail Customer"
    },
    {
        "created_at": "2014-12-18 12:59:40",
        "customer_group_code": "TestGroup",
        "mf_guid": "3wrlkhhptaqc3s8cdn3txm882xe609tz",
        "updated_at": "2014-12-18 12:59:40",
        "tax_class": "Retail Customer"
    }
]
JSON;

         return $json;
    }




    /*
********************This function is created by Peeter.
*/
    public function getNewsletterTemplateForPost()
    {
        $json = <<<JSON
[
    {
        "template_code": "jhjh",
        "template_sender_email": "juku@support.com",
        "template_sender_name": "jhjhh",
        "template_subject": "jhjh",
        "template_text": "<p>Follow this link to unsubscribe</p>\r\n<p>jkjkjj</p>\r\n<!-- This tag is for unsubscribe link  -->\r\n<p><a href=\"{{var subscriber.getUnsubscriptionLink()}}\">{{var subscriber.getUnsubscriptionLink()}}</a></p>",
        "template_type": "2"
    }
]
JSON;

         return $json;
    }


/*
********************This function is created by Peeter Tue 16 Dec 2014
*/
  public function getSystemDesignForPost()
  {
  $json = <<<JSON
[
    {
     

      "created_at": "2014-12-16T11:19:49+00:00",
      "date_from": "2014-12-11",
      "date_to": "2014-12-25",
      "design": "default/modern",

      "updated_at": "2014-12-16T11:19:49+00:00",
      "store": "default"
            
    }
]
JSON;

    return $json;

  }




    
} 