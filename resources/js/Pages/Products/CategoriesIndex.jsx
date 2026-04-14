import Container from "../../components/dashboard/Container";
import NavLinkBtn from "../../components/NavLinkBtn";
import CatLoop from "../../components/client/CatLoop";
import UserLayout from "../../Layouts/User/App";

export default function CategoriesIndex({ categories = [] }) {
    return (
        <UserLayout title="Category">
            <Container>
                <div>
                    <div>
                        <NavLinkBtn href={route("products.index")}>
                            All Product
                        </NavLinkBtn>
                        <br />
                    </div>

                    {categories.map((item) => (
                        <CatLoop key={item.id} item={item} style="font-bold" />
                    ))}
                </div>
            </Container>
        </UserLayout>
    );
}
