import NavLink from "../NavLink";

export default function Cat({ cat, active = false, height = 160 }) {
    if (!cat) return null;

    return (
        <div>
            <style
                dangerouslySetInnerHTML={{
                    __html: `
                        .cat_box {
                            position: relative;
                            display: block;
                            height: ${height}px;
                            border-radius: 12px;
                            overflow: hidden;
                        }

                        .cat_box img {
                            height: ${height}px;
                            object-fit: cover;
                            width: 100%;
                        }

                        .cat_box:hover img {
                            scale: 1.1;
                            transition: all linear .3s;
                            bottom: 0;
                            left: 0;
                        }

                        .cat_box .detail-box {
                            position: absolute;
                            bottom: 0;
                            width: 100%;
                            height: auto;
                            left: 0px;
                            background: linear-gradient(0deg, rgb(59, 59, 59), transparent);
                            vertical-align: middle;
                            display: flex;
                            flex-direction: column;
                            justify-content: start;
                            align-items: center;
                        }

                        .cat_box .detail-box {
                            color: white !important;
                            font-size: 14px !important;
                            font-weight: bold;
                        }

                        .fa_icon {
                            position: absolute;
                            top: 10px;
                            left: 10px;
                            font-size: 18px;
                            height: 25px;
                            width: 25px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            background: var(--brand-secondary);
                            border-radius: 50%;
                        }
                    `,
                }}
            />

            <div className={`px-2 mb-2 cat_box ${active ? "shadow" : ""}`}>
                <div className="cat_box border">
                    <NavLink className="" href={route("category.products", { cat: cat.slug || cat.name })}>
                        <img src={`/storage/${cat.image}`} alt={cat.name} />
                        <div className="detail-box">
                            <div className="w-full px-3 py-1 bold bg_primary text-center text-light product-title">
                                {cat.name}
                            </div>
                        </div>
                    </NavLink>
                </div>
            </div>
        </div>
    );
}
