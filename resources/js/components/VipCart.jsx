import NavLinkBtn from "@/Components/NavLinkBtn";
import Hr from "./Hr";

export default function VipCart({ item, style = {}, active = "", type }) {
    if (!item) {
        return (
            <div className="text-center alert alert-info">No Data Found !</div>
        );
    }

    const isActive = active === item.id;
    const href = route("user.package.checkout", { id: item.id });

    return (
        <div>
            {/* Keep ALL original styles */}
            <style
                dangerouslySetInnerHTML={{
                    __html: `
            .vip_cart {
                color: #000;
                overflow: hidden;
                transition: all linear .3s;
            }

            .vip_cart:hover {
                box-shadow: 0px 5px 5px #d9d9d9;
                transition: all linear .3s;
            }

            .vip_cart:hover .vip_button {
                background-color: var(--brand-primary);
                transition: all linear .3s;
                color: var(--brand-white);
            }

            .vip_cart .head {
                padding: 10px 8px 0px 8px;
                color: hsl(23, 100%, 65%);
            }

            .vip_cart a {
                color: #000;
            }

            .selected {
                border: 3px solid rgb(31, 118, 80) !important;
            }

            .unSelected {
                opacity: 4;
            }

            .selected_btn {
                background-color: rgb(31, 118, 80);
                color: white !important;
            }

            .position-fixed {
                position: fixed !important;
                top: 0;
                left: 0;
                z-index: 99;
                opacity: 1;
                transition: all linear .3s;
            }

            .position-hidden {
                position: fixed;
                top: -500%;
                left: 0;
                z-index: -99;
                opacity: 0;
                transition: all linear .3s;
            }
          `,
                }}
            />

            <div
                style={typeof style === "object" ? style : {}}
                className={`rounded-md shadow-lg vip_cart border br_primary text-center ${
                    isActive ? "selected" : "unSelected"
                }`}
            >
                <div className="text-center head bolder">
                    {item.name?.toUpperCase()}
                </div>

                <div className="px-3 pb-3">
                    <Hr />
                    <div
                        className="vip_price text_secondary"
                        style={{
                            fontSize: "35px",
                            fontWeight: "bolder",
                            lineHeight: "40px",
                        }}
                    >
                        {item.price}
                        <div
                            className="inline-block"
                            style={{
                                fontSize: "15px",
                                textAlign: "left",
                                lineHeight: "5px",
                                fontWeight: "300",
                                marginLeft: "5px",
                            }}
                        >
                            TK
                        </div>
                    </div>

                    <div
                        className="py-4 vip_info text_sm"
                        style={{ fontWeight: 300 }}
                    >
                        <div>{item.countdown} Minute daily time</div>
                        <div>{item.coin} TK per day</div>
                    </div>

                    {isActive ? (
                        <div className="w-full p-2 font-bold text-white uppercase bg-green-900 rounded-md text-md">
                            selected
                            <i className="mx-2 fas fa-check-circle"></i>
                        </div>
                    ) : (
                        <NavLinkBtn href={href}>View Details</NavLinkBtn>
                    )}
                </div>
            </div>
        </div>
    );
}
