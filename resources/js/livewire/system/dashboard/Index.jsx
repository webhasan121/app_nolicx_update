import Container from "../../../components/dashboard/Container";
import Hr from "../../../components/Hr";
import NavLink from "../../../components/NavLink";

function OverviewDiv({ title, children }) {
    return (
        <div
            className="rounded d-block shadow p-3 relative overflow-hidden"
            style={{ backgroundColor: "orange", zIndex: 1, color: "white" }}
        >
            <style
                dangerouslySetInnerHTML={{
                    __html: `
                        .div_wrapper {
                            position: absolute;
                            bottom: -100px;
                            right: -100px;
                            width: 200px;
                            height: 200px;
                            border-radius: 50%;
                            background: radial-gradient(rgb(12, 165, 94), transparent);
                            z-index: -1;
                        }

                        .div_wrapper::after {
                            content: "";
                            position: absolute;
                            width: 80px;
                            height: 80px;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            border-radius: 50%;
                            background: radial-gradient(green, transparent);
                        }
                    `,
                }}
            />

            <div className="text-md mb-3">{title}</div>

            <div className="text-end text-2xl">{children}</div>

            <div className="div_wrapper"></div>
        </div>
    );
}

export default function Index({
    userName,
    adm,
    vd,
    avd,
    rs,
    ars,
    ri,
    ari,
    userCount,
    vp,
    cat,
}) {
    return (
        <div>
            <Container>
                <div className="w-full text-md rounded-md mb-3 p-3">
                    Welcome Back ! {userName}
                    <p className="text-xs">Quick review what's goint on your store.</p>
                </div>

                <p className="mb-2 text-xs">Overall Details</p>
                <div
                    style={{
                        display: "grid",
                        gridTemplateColumns:
                            "repeat(auto-fill, minmax(150px, 1fr))",
                        gridGap: "20px",
                    }}
                >
                    <OverviewDiv title="Admins">{adm}</OverviewDiv>

                    <OverviewDiv title="Vendors">
                        <div>
                            {vd} / {avd}
                        </div>
                    </OverviewDiv>

                    <OverviewDiv title="Resellers">
                        <div>
                            {rs} / {ars}
                        </div>
                    </OverviewDiv>

                    <OverviewDiv title="Riders">
                        <div>
                            {ri} / {ari}
                        </div>
                    </OverviewDiv>

                    <OverviewDiv title="Users">
                        <div>{userCount}</div>
                    </OverviewDiv>

                    <OverviewDiv
                        title={
                            <div className="flex">
                                Products
                                <NavLink href={route("system.products.index")} className="ms-2">
                                    view
                                </NavLink>
                            </div>
                        }
                    >
                        <div>{vp}</div>
                    </OverviewDiv>

                    <OverviewDiv title="Category">
                        <div>{cat}</div>
                    </OverviewDiv>
                </div>
                <Hr />

                <div></div>
            </Container>
        </div>
    );
}
