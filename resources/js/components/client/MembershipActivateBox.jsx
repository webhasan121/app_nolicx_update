import NavLink from "../NavLink";
import useTranslation from "../../hooks/useTranslation";

export default function MembershipActivateBox({ vendorActive, resellerActive }) {
    const { t } = useTranslation();

    return (
        <div>
            {vendorActive && (
                <div className="alert alert-success">
                    <h6>{t("Hello,")}</h6>
                    <p>
                        {t("Your request for vendor, name of")}{" "}
                        <strong className="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {vendorActive.shop_name_bn ?? "N/A"} / {vendorActive.shop_name_en ?? "N/A"}
                        </strong>
                        {" "}{t("with")}{" "}
                        <strong className="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {vendorActive.system_get_comission ?? "0"}%
                        </strong>
                        {" "}{t("comission share, is active now.")}
                    </p>
                    <NavLink href={route("dashboard")}>{t("Go To Dashboard")}</NavLink>
                </div>
            )}

            {resellerActive && (
                <div className="alert alert-success">
                    <h6>{t("Hello,")}</h6>
                    <p>
                        {t("Your request for reseller, name of")}{" "}
                        <strong className="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {resellerActive.shop_name_bn ?? "N/A"} / {resellerActive.shop_name_en ?? "N/A"}
                        </strong>
                        {" "}{t("with")}{" "}
                        <strong className="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {resellerActive.system_get_comission ?? "0"}%
                        </strong>
                        {" "}{t("comission share, is active now.")}
                    </p>
                </div>
            )}
        </div>
    );
}
