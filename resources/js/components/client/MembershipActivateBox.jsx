import NavLink from "../NavLink";

export default function MembershipActivateBox({ vendorActive, resellerActive }) {
    return (
        <div>
            {vendorActive && (
                <div className="px-4 py-3 mb-3 alert alert-success text-green-700 bg-green-100 border border-green-400 rounded">
                    <h6 className="mb-2 font-semibold">Hello,</h6>
                    <p>
                        Your request for vendor, name of
                        <strong className="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {vendorActive.shop_name_bn ?? "N/A"} / {vendorActive.shop_name_en ?? "N/A"}
                        </strong>
                        with
                        <strong className="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {vendorActive.system_get_comission ?? "0"}%
                        </strong>
                        comission share, is active now.
                    </p>
                    <NavLink href={route("dashboard")}>Go To Dashboard</NavLink>
                </div>
            )}

            {resellerActive && (
                <div className="px-4 py-3 alert alert-success text-green-700 bg-green-100 border border-green-400 rounded">
                    <h6 className="mb-2 font-semibold">Hello,</h6>
                    <p>
                        Your request for reseller, name of
                        <strong className="px-3 py-1 mx-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {resellerActive.shop_name_bn ?? "N/A"} / {resellerActive.shop_name_en ?? "N/A"}
                        </strong>
                        with
                        <strong className="px-3 py-1 text-white bg-gray-800 rounded-lg shadow-sm">
                            {resellerActive.system_get_comission ?? "0"}%
                        </strong>
                        comission share, is active now.
                    </p>
                </div>
            )}
        </div>
    );
}
