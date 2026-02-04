--
-- PostgreSQL database dump
--

\restrict AwaiabNO7KoMDy4PgFLyPCdHBtGtacO6xRXgecZbgHXo62ntvbDPOacuJ22Cybo

-- Dumped from database version 18.0 (Debian 18.0-1.pgdg13+3)
-- Dumped by pg_dump version 18.0

-- Started on 2026-02-04 01:10:08 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 231 (class 1255 OID 16516)
-- Name: join_user_to_event(bigint, bigint); Type: PROCEDURE; Schema: public; Owner: docker
--

CREATE PROCEDURE public.join_user_to_event(IN p_user_id bigint, IN p_event_id bigint)
    LANGUAGE plpgsql
    AS $$
BEGIN
	INSERT INTO event_users (user_id, event_id)
	VALUES (p_user_id, p_event_id)
	ON CONFLICT (user_id, event_id) DO NOTHING;
END;
$$;


ALTER PROCEDURE public.join_user_to_event(IN p_user_id bigint, IN p_event_id bigint) OWNER TO docker;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 16428)
-- Name: comments; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.comments (
    id integer NOT NULL,
    user_id integer NOT NULL,
    message_id integer NOT NULL,
    content text NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.comments OWNER TO docker;

--
-- TOC entry 223 (class 1259 OID 16427)
-- Name: comments_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.comments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.comments_id_seq OWNER TO docker;

--
-- TOC entry 3516 (class 0 OID 0)
-- Dependencies: 223
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.comments_id_seq OWNED BY public.comments.id;


--
-- TOC entry 220 (class 1259 OID 16391)
-- Name: users; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.users (
    id integer NOT NULL,
    email character varying(60) NOT NULL,
    first_name character varying(50) NOT NULL,
    surname character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    active boolean DEFAULT false NOT NULL,
    admin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.users OWNER TO docker;

--
-- TOC entry 229 (class 1259 OID 16505)
-- Name: comments_with_user; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.comments_with_user AS
 SELECT c.id,
    c.content,
    c.created_at,
    c.message_id,
    u.first_name,
    u.surname
   FROM (public.comments c
     JOIN public.users u ON ((u.id = c.user_id)));


ALTER VIEW public.comments_with_user OWNER TO docker;

--
-- TOC entry 227 (class 1259 OID 16472)
-- Name: event_users; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.event_users (
    event_id integer NOT NULL,
    user_id integer NOT NULL,
    joined_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.event_users OWNER TO docker;

--
-- TOC entry 226 (class 1259 OID 16453)
-- Name: events; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.events (
    id integer NOT NULL,
    created_by integer NOT NULL,
    title character varying(100) NOT NULL,
    description text,
    event_date timestamp without time zone NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.events OWNER TO docker;

--
-- TOC entry 225 (class 1259 OID 16452)
-- Name: events_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.events_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.events_id_seq OWNER TO docker;

--
-- TOC entry 3517 (class 0 OID 0)
-- Dependencies: 225
-- Name: events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.events_id_seq OWNED BY public.events.id;


--
-- TOC entry 228 (class 1259 OID 16496)
-- Name: events_with_users; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.events_with_users AS
SELECT
    NULL::integer AS id,
    NULL::character varying(100) AS title,
    NULL::text AS description,
    NULL::timestamp without time zone AS event_date,
    NULL::character varying(50) AS creator_first_name,
    NULL::character varying(50) AS creator_surname,
    NULL::json AS users;


ALTER VIEW public.events_with_users OWNER TO docker;

--
-- TOC entry 222 (class 1259 OID 16409)
-- Name: messages; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.messages (
    id integer NOT NULL,
    user_id integer NOT NULL,
    content text NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    title character varying(255) NOT NULL
);


ALTER TABLE public.messages OWNER TO docker;

--
-- TOC entry 221 (class 1259 OID 16408)
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.messages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.messages_id_seq OWNER TO docker;

--
-- TOC entry 3518 (class 0 OID 0)
-- Dependencies: 221
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.messages_id_seq OWNED BY public.messages.id;


--
-- TOC entry 230 (class 1259 OID 16524)
-- Name: messages_with_author_and_comments; Type: VIEW; Schema: public; Owner: docker
--

CREATE VIEW public.messages_with_author_and_comments AS
 SELECT m.id,
    m.title,
    m.content,
    m.created_at,
    author.first_name AS author_first_name,
    author.surname AS author_surname,
    COALESCE(json_agg(jsonb_build_object('content', cwu.content, 'created_at', cwu.created_at, 'first_name', cwu.first_name, 'surname', cwu.surname) ORDER BY cwu.created_at) FILTER (WHERE (cwu.id IS NOT NULL)), '[]'::json) AS comments
   FROM ((public.messages m
     JOIN public.users author ON ((author.id = m.user_id)))
     LEFT JOIN public.comments_with_user cwu ON ((cwu.message_id = m.id)))
  GROUP BY m.id, m.title, m.content, m.created_at, author.first_name, author.surname;


ALTER VIEW public.messages_with_author_and_comments OWNER TO docker;

--
-- TOC entry 219 (class 1259 OID 16390)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO docker;

--
-- TOC entry 3519 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3327 (class 2604 OID 16431)
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.comments ALTER COLUMN id SET DEFAULT nextval('public.comments_id_seq'::regclass);


--
-- TOC entry 3329 (class 2604 OID 16456)
-- Name: events id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.events ALTER COLUMN id SET DEFAULT nextval('public.events_id_seq'::regclass);


--
-- TOC entry 3325 (class 2604 OID 16412)
-- Name: messages id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.messages ALTER COLUMN id SET DEFAULT nextval('public.messages_id_seq'::regclass);


--
-- TOC entry 3321 (class 2604 OID 16394)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3507 (class 0 OID 16428)
-- Dependencies: 224
-- Data for Name: comments; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.comments (id, user_id, message_id, content, created_at) FROM stdin;
5	1	5	tutaj komentarz	2026-02-04 01:06:00.243886
6	1	5	i tutaj	2026-02-04 01:06:03.637687
\.


--
-- TOC entry 3510 (class 0 OID 16472)
-- Dependencies: 227
-- Data for Name: event_users; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.event_users (event_id, user_id, joined_at) FROM stdin;
5	1	2026-02-04 01:07:09.445702
6	1	2026-02-04 01:07:58.08783
6	11	2026-02-04 01:08:18.784014
\.


--
-- TOC entry 3509 (class 0 OID 16453)
-- Dependencies: 226
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.events (id, created_by, title, description, event_date, created_at) FROM stdin;
5	1	Wyjście w góry	Idziemy sobie w Tatry (zimą)	2026-02-07 00:00:00	2026-02-04 01:07:06.262009
6	1	Kino	Idziemy do kina na IronLung	2026-02-06 00:00:00	2026-02-04 01:07:54.482301
\.


--
-- TOC entry 3505 (class 0 OID 16409)
-- Dependencies: 222
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.messages (id, user_id, content, created_at, title) FROM stdin;
5	1	To jest nowa wiadomość.            	2026-02-04 01:05:51.74519	Nowa wiadomość
6	1	Oto następna wiadomośc, napisana po nowej wiadomości.      	2026-02-04 01:06:30.930324	Kolejna wiadomość
\.


--
-- TOC entry 3503 (class 0 OID 16391)
-- Dependencies: 220
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.users (id, email, first_name, surname, password, created_at, active, admin) FROM stdin;
1	test@test.com	Test	Test	$2a$12$l1T8xUk13f50cEWriv9QTuzyXL62F1RYZ6mdFiN7HAjI2.GLkH5Mm	2026-01-29 20:47:13.534191	t	t
11	enabled@user.com	Enabled	User	$2y$12$v7NTertWrH5TbCw.nVXlhu5ftmv20DothIkbRuQfpEAS62Wzi9zBy	2026-02-04 01:05:07.498517	t	f
12	disabled@user.com	Disabled	User	$2y$12$WIpz.N3Jl9DM/0HMzfUIduR0NIdfgSAdl5wcZtdwcj8J01Xs9t65m	2026-02-04 01:05:28.934477	f	f
\.


--
-- TOC entry 3520 (class 0 OID 0)
-- Dependencies: 223
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.comments_id_seq', 6, true);


--
-- TOC entry 3521 (class 0 OID 0)
-- Dependencies: 225
-- Name: events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.events_id_seq', 6, true);


--
-- TOC entry 3522 (class 0 OID 0)
-- Dependencies: 221
-- Name: messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.messages_id_seq', 6, true);


--
-- TOC entry 3523 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.users_id_seq', 12, true);


--
-- TOC entry 3339 (class 2606 OID 16441)
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- TOC entry 3343 (class 2606 OID 16480)
-- Name: event_users event_users_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.event_users
    ADD CONSTRAINT event_users_pkey PRIMARY KEY (event_id, user_id);


--
-- TOC entry 3345 (class 2606 OID 16515)
-- Name: event_users event_users_unique; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.event_users
    ADD CONSTRAINT event_users_unique UNIQUE (user_id, event_id);


--
-- TOC entry 3341 (class 2606 OID 16466)
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id);


--
-- TOC entry 3337 (class 2606 OID 16421)
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- TOC entry 3333 (class 2606 OID 16407)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3335 (class 2606 OID 16405)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3499 (class 2618 OID 16499)
-- Name: events_with_users _RETURN; Type: RULE; Schema: public; Owner: docker
--

CREATE OR REPLACE VIEW public.events_with_users AS
 SELECT e.id,
    e.title,
    e.description,
    e.event_date,
    creator.first_name AS creator_first_name,
    creator.surname AS creator_surname,
    COALESCE(json_agg(json_build_object('id', u.id, 'first_name', u.first_name, 'surname', u.surname) ORDER BY u.surname, u.first_name) FILTER (WHERE (u.id IS NOT NULL)), '[]'::json) AS users
   FROM (((public.events e
     JOIN public.users creator ON ((creator.id = e.created_by)))
     LEFT JOIN public.event_users eu ON ((eu.event_id = e.id)))
     LEFT JOIN public.users u ON ((u.id = eu.user_id)))
  GROUP BY e.id, creator.first_name, creator.surname;


--
-- TOC entry 3347 (class 2606 OID 16447)
-- Name: comments comments_message_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_message_id_fkey FOREIGN KEY (message_id) REFERENCES public.messages(id) ON DELETE CASCADE;


--
-- TOC entry 3348 (class 2606 OID 16442)
-- Name: comments comments_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3350 (class 2606 OID 16481)
-- Name: event_users event_users_event_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.event_users
    ADD CONSTRAINT event_users_event_id_fkey FOREIGN KEY (event_id) REFERENCES public.events(id) ON DELETE CASCADE;


--
-- TOC entry 3351 (class 2606 OID 16486)
-- Name: event_users event_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.event_users
    ADD CONSTRAINT event_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3349 (class 2606 OID 16467)
-- Name: events events_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3346 (class 2606 OID 16422)
-- Name: messages messages_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


-- Completed on 2026-02-04 01:10:08 UTC

--
-- PostgreSQL database dump complete
--

\unrestrict AwaiabNO7KoMDy4PgFLyPCdHBtGtacO6xRXgecZbgHXo62ntvbDPOacuJ22Cybo

