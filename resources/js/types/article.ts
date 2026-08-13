export type ArticleAuthor = {
    id: number;
    name: string;
};

export type Taxonomy = {
    id: number;
    name: string;
    slug: string;
};

export type Article = {
    id: number;
    title: string;
    slug: string;
    image_url: string;
    image_alt: string;
    summary: string;
    body: string;
    rating: number;
    affiliate_url: string | null;
    published_at: string | null;
    created_at: string | null;
    updated_at: string | null;
    author: ArticleAuthor;
    category: Taxonomy;
    tags: Taxonomy[];
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
};

export type CategoryOption = {
    id: number;
    name: string;
};
