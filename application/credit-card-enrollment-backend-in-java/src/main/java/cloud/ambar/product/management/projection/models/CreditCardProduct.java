package cloud.ambar.product.management.projection.models;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;
import jakarta.persistence.Id;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.data.mongodb.core.mapping.Document;

@Data
@NoArgsConstructor
@Document(collection = "ProductListItems")
@JsonIgnoreProperties(ignoreUnknown = true)
public class CreditCardProduct {
    @Id
    private String id;
    private String name;
    @JsonProperty("isActive")
    private boolean active;
    private String reward;
    private String paymentCycle;
    private int annualFeeInCents;
    private int creditLimitInCents;
    private String hexColor;
}
