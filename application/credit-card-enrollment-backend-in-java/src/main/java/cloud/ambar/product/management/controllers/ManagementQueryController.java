package cloud.ambar.product.management.controllers;

import cloud.ambar.product.management.projection.models.CreditCardProduct;
import cloud.ambar.product.management.query.ProductManagementQueryService;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import lombok.RequiredArgsConstructor;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

/**
 * This controller will handle endpoints related to querying details about products for the front end.
 * These endpoints do not handle any commands and just return things back from the ReadModelRepository as
 * written by projections and reactions.
 * This is the Read side of our application
 * Requests to handle:
 *  - ListProducts
 */
@RestController
@RequiredArgsConstructor
public class ManagementQueryController {
    private static final Logger log = LogManager.getLogger(ManagementQueryController.class);

    private final ProductManagementQueryService productManagementQueryService;

    private final ObjectMapper objectMapper;

    @PostMapping(value = "/api/v1/credit_card_product/product/list-items")
    public String listItems() throws JsonProcessingException {
        log.info("Listing all products from ProjectionRepository");
        List<CreditCardProduct> creditCardProducts = productManagementQueryService.getAllCreditCardProducts();

        return objectMapper.writeValueAsString(creditCardProducts);
    }

}
